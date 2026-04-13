<x-app-layout>
    <x-slot name="title">Upload Laporan PKL</x-slot>

    <div class="space-y-6 animate-fade-in pb-12">
        
        {{-- Premium Header --}}
        <div class="relative overflow-hidden rounded-[2rem] p-8 border border-white/20 shadow-lg bg-gradient-to-r from-emerald-900 via-teal-900 to-sky-900 isolate">
            <div class="absolute -top-12 -right-12 w-64 h-64 bg-emerald-500 rounded-full mix-blend-multiply filter blur-[60px] opacity-50 animate-pulse"></div>
            <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-teal-500 rounded-full mix-blend-multiply filter blur-[60px] opacity-50 animate-pulse" style="animation-delay: 2s;"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 shadow-inner flex items-center justify-center backdrop-blur-md">
                        <i class="fa-solid fa-file-arrow-up text-2xl text-emerald-400"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                            Pemberkasan Laporan
                        </h2>
                        <p class="text-emerald-100/80 font-medium text-sm mt-1">Unggah dokumen akhir pelaksanaan Praktik Kerja Lapangan Anda.</p>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn-secondary !rounded-xl !px-4 !py-2 !text-sm !font-bold backdrop-blur-md bg-white/10 text-white border-white/20 hover:bg-white/20">
                        <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        @if(!$proposal)
            {{-- Error State: No Proposal --}}
            <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/30 rounded-[2rem] p-10 text-center flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-amber-100 dark:bg-amber-900/20 text-amber-500 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-triangle-exclamation text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-amber-700 dark:text-amber-400 mb-2">Data Pengajuan PKL Belum Tersedia</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-6">
                    Anda belum memasukkan data instansi dan surat pendaftaran. Lengkapi formulir pendaftaran sebelum mengunggah laporan!
                </p>
                <a href="{{ route('mahasiswa.proposal.index') }}" class="px-6 py-2.5 rounded-xl bg-amber-600 border border-amber-500 text-white text-sm font-bold shadow-[0_0_20px_rgba(245,158,11,0.3)] hover:bg-amber-500 transition-colors">Input Data Sekarang</a>
            </div>
        @elseif($openingHours && !$openingHours->isLaporanBuka())
            {{-- Error State: Closed --}}
            <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30 rounded-[2rem] p-10 text-center flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-900/20 text-red-500 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-lock text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-red-700 dark:text-red-400 mb-2">Masa Upload Laporan Ditutup</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-sm mx-auto">
                    Periode unggah berkas laporan berdasarkan kalender akademik: <br>
                    <span class="font-bold text-gray-800 dark:text-gray-200 mt-2 block">
                        {{ $openingHours->open_laporan?->format('d M Y') ?? '?' }} — {{ $openingHours->close_laporan?->format('d M Y') ?? '?' }}
                    </span>
                </p>
                <a href="{{ route('mahasiswa.dashboard') }}" class="mt-6 px-6 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-gray-200 dark:border-surface-700 text-sm font-bold shadow-sm hover:bg-gray-50 dark:hover:bg-surface-700 transition-colors">Kembali ke Dashboard</a>
            </div>
        @elseif($proposal->nilai !== null)
            {{-- Error State: Graded --}}
            <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/30 rounded-[2rem] p-10 text-center flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-medal text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-blue-700 dark:text-blue-400 mb-2">Penilaian Selesai</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                    Selamat, proposal & berkas PKL Anda telah dinilai. Seluruh file dokumen yang telah diunggah kini dikunci secara permanen dan <strong>tidak dapat dihapus atau diganti lagi</strong> untuk menjaga integritas data penilaian akhir.
                </p>
                <div class="mt-6 flex gap-4">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="px-6 py-2.5 rounded-xl bg-blue-600 border border-blue-500 text-white text-sm font-bold shadow-[0_0_20px_rgba(59,130,246,0.3)] hover:bg-blue-500 transition-colors">Cek Nilai di Dashboard</a>
                </div>
            </div>
        @else
            {{-- Form Upload Area --}}
            <div class="bg-white dark:bg-surface-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-surface-700 overflow-hidden relative">
                
                <div class="p-8 border-b border-gray-100 dark:border-surface-700 bg-gray-50/50 dark:bg-surface-850/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-base flex items-center gap-2">
                            <i class="fa-solid fa-folder-open text-emerald-500"></i> Dokumen Final Assessment
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pastikan nama file jelas. Anda dapat memperbarui dokumen selama periode upload belum ditutup.</p>
                    </div>

                    {{-- Separate Form for Reset to avoid nested forms --}}
                    <form id="reset-laporan-form" method="POST" action="{{ route('mahasiswa.laporan.reset') }}">
                        @csrf
                        <button type="button" onclick="confirmReset()" class="group px-4 py-2 rounded-xl bg-rose-50 dark:bg-rose-900/10 border border-rose-200 dark:border-rose-800/30 text-rose-600 dark:text-rose-400 text-sm font-bold hover:bg-rose-600 hover:text-white hover:border-rose-600 transition-all">
                            <i class="fa-solid fa-trash-can mr-1.5 group-hover:animate-bounce"></i> Reset & Hapus
                        </button>
                    </form>
                </div>

                <form method="POST" action="{{ route('mahasiswa.laporan.upload') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="p-8">
                        @php
                            $documents = [
                                'lp' => ['Laporan Akhir PKL', 'Keseluruhan bab, daftar pustaka, hingga lampiran final.'],
                                'lpp' => ['Lembar Penilaian Perusahaan', 'Scan form nilai yang dicap & ditandatangani mentor industri.'],
                                'skp' => ['Surat Keterangan Selesai', 'Scan sertifikat atau surat resmi bukti telah menyelesaikan PKL.']
                            ];
                        @endphp
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($documents as $field => $info)
                            <div class="flex flex-col relative group">
                                <div class="bg-gray-50 dark:bg-surface-900 rounded-2xl border-2 {{ $proposal->{$field} ? 'border-emerald-200 dark:border-emerald-800/40 bg-emerald-50/30 dark:bg-emerald-900/5' : 'border-dashed border-gray-200 dark:border-surface-600 hover:border-emerald-300 dark:hover:border-emerald-700/50' }} p-6 flex-1 flex flex-col items-center text-center transition-colors">
                                    
                                    {{-- Icon Indicator --}}
                                    <div class="relative mb-4">
                                        <div class="w-16 h-16 rounded-2xl {{ $proposal->{$field} ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600' : 'bg-white dark:bg-surface-800 border border-gray-200 dark:border-surface-600 text-gray-400' }} flex items-center justify-center shadow-sm">
                                            <i class="fa-solid {{ $proposal->{$field} ? 'fa-file-circle-check' : 'fa-file-pdf' }} text-3xl"></i>
                                        </div>
                                        @if($proposal->{$field})
                                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-emerald-500 rounded-full border-2 border-white dark:border-surface-900 text-white flex items-center justify-center shadow-md">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <h4 class="font-bold text-gray-900 dark:text-gray-100 text-sm mb-1">{{ $info[0] }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-5 leading-relaxed">{{ $info[1] }}</p>

                                    <div class="mt-auto w-full">
                                        <label for="{{ $field }}-upload" class="flex flex-col items-center justify-center w-full py-2.5 px-4 bg-white dark:bg-surface-800 border border-gray-200 dark:border-surface-600 rounded-xl cursor-pointer hover:bg-gray-50 dark:hover:bg-surface-700 hover:text-emerald-600 transition-colors">
                                            <span id="{{ $field }}-filename" class="text-xs font-bold uppercase tracking-widest truncate w-full px-2 text-center">{{ $proposal->{$field} ? 'Ganti File (PDF)' : 'Pilih File (PDF)' }}</span>
                                            <input id="{{ $field }}-upload" name="{{ $field }}" type="file" class="sr-only" accept=".pdf" onchange="document.getElementById('{{ $field }}-filename').innerText = this.files[0] ? this.files[0].name : '{{ $proposal->{$field} ? 'Ganti File (PDF)' : 'Pilih File (PDF)' }}'">
                                        </label>
                                    </div>

                                    @if($proposal->{$field})
                                        <div class="w-full mt-3 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/30 flex items-center justify-center gap-1.5 overflow-hidden">
                                            <i class="fa-solid fa-link text-[10px] text-emerald-500 shrink-0"></i>
                                            <a href="{{ route('files.serve', $proposal->{$field}) }}" target="_blank" class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 hover:underline truncate">
                                                Lihat Berkas Aktif
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Form Footer Actions --}}
                    <div class="bg-gray-50/50 dark:bg-surface-800/50 border-t border-gray-100 dark:border-surface-700 px-8 py-5 flex items-center justify-between flex-wrap gap-4">
                        <div class="text-xs text-gray-500 font-medium">
                            <i class="fa-solid fa-circle-info text-sky-500 mr-1"></i> Maksimal ukuran per file adalah 40MB. Format wajib .PDF
                        </div>
                        <button type="submit" class="btn-primary inline-flex items-center gap-2 shadow-[0_0_15px_rgba(16,185,129,0.3)] hover:shadow-[0_0_25px_rgba(16,185,129,0.5)] transition-shadow {{ (!$proposal->lp && !$proposal->lpp && !$proposal->skp) ? 'animate-[pulse_2s_infinite]' : '' }} bg-emerald-600 hover:bg-emerald-500 border-emerald-500">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            Mulai Upload
                        </button>
                    </div>

                </form>
            </div>
            
            @if($errors->any())
                <div class="mt-6 bg-red-50 dark:bg-red-900/10 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                        <div>
                            <h3 class="text-sm font-bold text-red-800 dark:text-red-400 mb-1">Upload Gagal</h3>
                            <ul class="text-xs text-red-700 dark:text-red-300 list-disc list-inside space-y-1">
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

    <!-- SweetAlert2 untuk Konfirmasi Reset -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmReset() {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Seluruh 3 dokumen laporan yang telah diunggah akan dihapus secara permanen dari server!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mencegah double click
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    document.getElementById('reset-laporan-form').submit();
                }
            });
        }
    </script>
</x-app-layout>
