<x-app-layout>
    <x-slot name="title">Upload Laporan PKL</x-slot>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Pemberkasan Laporan PKL</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Unggah dokumen akhir pelaksanaan Praktik Kerja Lapangan Anda.</p>
            </div>
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if(!$proposal)
            {{-- No proposal yet --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-12 text-center">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation text-2xl text-amber-500"></i>
                </div>
                <p class="text-base font-semibold text-gray-900 dark:text-white mb-1">Data Pengajuan PKL Belum Tersedia</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-5">Lengkapi formulir pendaftaran sebelum mengunggah laporan.</p>
                <a href="{{ route('mahasiswa.proposal.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                    <i class="fa-solid fa-plus"></i> Input Data Sekarang
                </a>
            </div>
        @elseif($proposal->nilai !== null && $proposal->nilai > 0)
            {{-- Already graded --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-12 text-center">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-medal text-2xl text-blue-500"></i>
                </div>
                <p class="text-base font-semibold text-gray-900 dark:text-white mb-1">Penilaian Selesai</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-5">
                    Proposal & berkas PKL Anda telah dinilai. Dokumen dikunci permanen untuk menjaga integritas data.
                </p>
                <a href="{{ route('mahasiswa.dashboard') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                    <i class="fa-solid fa-arrow-right"></i> Cek Nilai
                </a>
            </div>
        @else
            {{-- Upload Form --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Dokumen Final</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Anda dapat memperbarui dokumen selama periode upload masih dibuka.</p>
                    </div>

                    {{-- Reset form (separate from upload form) --}}
                    <form id="reset-laporan-form" method="POST" action="{{ route('mahasiswa.laporan.reset') }}">
                        @csrf
                        <button type="button" onclick="confirmReset()"
                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/30 text-rose-700 dark:text-rose-400 text-xs font-medium border border-rose-200 dark:border-rose-800/30 transition-colors">
                            <i class="fa-solid fa-trash-can"></i> Reset & Hapus
                        </button>
                    </form>
                </div>

                <form method="POST" action="{{ route('mahasiswa.laporan.upload') }}" enctype="multipart/form-data"
                      x-data="{
                          previewUrls: { lp: '', lpp: '', skp: '' },
                          currentPreview: '',
                          currentPreviewName: '',
                          showPreview: false,
                          handleFileChange(event, field) {
                              // Revoke old URL if exists
                              if (this.previewUrls[field]) {
                                  URL.revokeObjectURL(this.previewUrls[field]);
                              }

                              const file = event.target.files[0];
                              if (file) {
                                  // Update filename display
                                  document.getElementById(field + '-filename').innerText = file.name;
                                  // Create preview URL
                                  this.previewUrls[field] = URL.createObjectURL(file);
                              } else {
                                  const hasExisting = {{ Js::from(collect(['lp', 'lpp', 'skp'])->mapWithKeys(fn($f) => [$f => (bool)($proposal->{$f} ?? false)])->toArray()) }};
                                  document.getElementById(field + '-filename').innerText = hasExisting[field] ? 'Ganti File' : 'Pilih File (PDF)';
                                  this.previewUrls[field] = '';
                              }
                          },
                          openPreview(field, name) {
                              if (this.previewUrls[field]) {
                                  this.currentPreview = this.previewUrls[field];
                                  this.currentPreviewName = name;
                                  this.showPreview = true;
                              }
                          },
                          closePreview() {
                              this.showPreview = false;
                          }
                      }">
                    @csrf

                    {{-- Period Closed Warning --}}
                    @if($openingHours && !$openingHours->isLaporanBuka())
                        <div class="p-4 mx-6 mt-6 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 rounded-lg">
                            <div class="flex gap-3">
                                <i class="fa-solid fa-lock text-amber-500 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-1">Periode Upload Laporan Ditutup</p>
                                    <p class="text-xs text-amber-700 dark:text-amber-400">Anda dapat melihat dokumen yang telah diupload tetapi tidak dapat mengubahnya. Periode: {{ $openingHours->open_laporan?->format('d M Y') ?? '?' }} — {{ $openingHours->close_laporan?->format('d M Y') ?? '?' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="p-6">
                        @php
                            $documents = [
                                'lp' => ['Laporan Akhir PKL', 'Keseluruhan bab, daftar pustaka, hingga lampiran final.'],
                                'lpp' => ['Lembar Penilaian Perusahaan', 'Scan form nilai bercap dan tandatangan mentor industri.'],
                                'skp' => ['Surat Keterangan Selesai', 'Scan sertifikat atau surat resmi bukti telah selesai PKL.'],
                            ];
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach($documents as $field => $info)
                            <div @class([
                                'rounded-lg border p-5 flex flex-col',
                                'border-emerald-200 dark:border-emerald-800/30 bg-emerald-50/50 dark:bg-emerald-900/10' => $proposal->{$field},
                                'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900' => !$proposal->{$field},
                            ])>
                                <div class="flex items-start justify-between mb-3">
                                    <div class="w-10 h-10 rounded-md flex items-center justify-center
                                                {{ $proposal->{$field}
                                                    ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                                                    : 'bg-gray-100 dark:bg-gray-700 text-gray-400' }}">
                                        <i class="fa-solid {{ $proposal->{$field} ? 'fa-file-circle-check' : 'fa-file-pdf' }} text-xl"></i>
                                    </div>
                                    @if($proposal->{$field})
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30">
                                            <i class="fa-solid fa-check text-[10px]"></i> Terupload
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ $info[0] }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 leading-relaxed">{{ $info[1] }}</p>

                                <div class="mt-auto space-y-2">
                                    <label for="{{ $field }}-upload"
                                           class="flex items-center justify-center w-full py-2 px-3 bg-white dark:bg-gray-800 border rounded-md text-xs font-medium text-gray-700 dark:text-gray-300 transition-colors {{ $openingHours && !$openingHours->isLaporanBuka() ? 'cursor-not-allowed opacity-60' : 'cursor-pointer hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700 hover:text-blue-700 dark:hover:text-blue-400' }} @error($field) border-red-500 @else border-gray-300 dark:border-gray-600 @enderror">
                                        <i class="fa-solid fa-upload mr-2 text-[10px]"></i>
                                        <span id="{{ $field }}-filename" class="truncate">{{ $proposal->{$field} ? 'Ganti File' : 'Pilih File (PDF)' }}</span>
                                    </label>
                                    <input id="{{ $field }}-upload" name="{{ $field }}" type="file" class="sr-only fixed" accept=".pdf"
                                           {{ $openingHours && !$openingHours->isLaporanBuka() ? 'disabled' : '' }}
                                           @change="handleFileChange($event, '{{ $field }}')">

                                    {{-- Preview button for newly selected file --}}
                                    <button type="button" x-show="previewUrls.{{ $field }}" x-cloak
                                            @click="openPreview('{{ $field }}', '{{ $info[0] }}')"
                                            class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium transition-colors">
                                        <i class="fa-solid fa-eye text-[10px]"></i>
                                        Preview PDF
                                    </button>

                                    @error($field)
                                        <div class="flex items-center gap-2 px-3 py-2 rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30">
                                            <i class="fa-solid fa-triangle-exclamation text-red-500 text-xs shrink-0"></i>
                                            <p class="text-xs font-medium text-red-700 dark:text-red-400">{{ $message }}</p>
                                        </div>
                                    @enderror

                                    @if($proposal->{$field})
                                        <a href="{{ route('files.serve', encryptUrl($proposal->{$field})) }}" target="_blank"
                                           class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/30 text-emerald-700 dark:text-emerald-400 text-xs font-medium transition-colors truncate">
                                            <i class="fa-solid fa-external-link-alt text-[10px]"></i>
                                            <span class="truncate">Lihat Berkas</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i>
                            Maksimal ukuran per file adalah 40 MB. Format wajib PDF.
                        </p>
                        @if($openingHours && !$openingHours->isLaporanBuka())
                            <button type="button" disabled
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-md bg-gray-400 text-white text-sm font-medium cursor-not-allowed opacity-60">
                                <i class="fa-solid fa-lock"></i>
                                Periode Ditutup
                            </button>
                        @else
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                Unggah Berkas
                            </button>
                        @endif
                    </div>

                    {{-- PDF Preview Modal --}}
                    <div x-show="showPreview" x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-75"
                         @click.self="closePreview()">
                        <div class="relative w-full max-w-6xl h-[90vh] bg-white dark:bg-gray-800 rounded-lg shadow-2xl flex flex-col">
                            {{-- Modal Header --}}
                            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Preview: <span x-text="currentPreviewName"></span>
                                </h3>
                                <button type="button" @click="closePreview()"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                    <i class="fa-solid fa-times text-xl"></i>
                                </button>
                            </div>
                            {{-- Modal Body --}}
                            <div class="flex-1 overflow-hidden">
                                <iframe :src="currentPreview" class="w-full h-full border-0"></iframe>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30 rounded-lg p-4">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-red-800 dark:text-red-300 mb-1">Upload Gagal</p>
                            <ul class="text-xs text-red-700 dark:text-red-400 list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    <script>
        function confirmReset() {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: 'Seluruh 3 dokumen laporan yang telah diunggah akan dihapus secara permanen.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus Semua',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => Swal.showLoading(),
                    });
                    document.getElementById('reset-laporan-form').submit();
                }
            });
        }
    </script>
</x-app-layout>
