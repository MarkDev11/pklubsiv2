<x-app-layout>
    <x-slot name="title">Form Data PKL</x-slot>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $proposal ? 'Perbarui Data PKL' : 'Pendaftaran Data PKL' }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lengkapi informasi akademik dan institusi pelaksana PKL Anda.</p>
            </div>
            <a href="{{ route('mahasiswa.dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if($openingHours && !$openingHours->isPendaftaranBuka() && !$proposal)
            {{-- Closed --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-12 text-center">
                <div class="w-16 h-16 bg-red-50 dark:bg-red-900/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-lock text-2xl text-red-500"></i>
                </div>
                <p class="text-base font-semibold text-gray-900 dark:text-white mb-1">Masa Pengisian Data Ditutup</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    Periode resmi:
                    <span class="block mt-1 text-gray-700 dark:text-gray-300 font-medium">
                        {{ $openingHours->open_time?->format('d M Y') ?? '?' }} — {{ $openingHours->close_time?->format('d M Y') ?? '?' }}
                    </span>
                </p>
            </div>
        @elseif($proposal && $proposal->nilai !== null && $proposal->nilai > 0)
            {{-- Already graded --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-12 text-center">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-medal text-2xl text-blue-500"></i>
                </div>
                <p class="text-base font-semibold text-gray-900 dark:text-white mb-1">Penilaian Selesai</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-5">
                    PKL Anda telah dinilai. Data dasar pengajuan dikunci permanen.
                </p>
                <a href="{{ route('mahasiswa.dashboard') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                    <i class="fa-solid fa-arrow-right"></i> Cek Nilai
                </a>
            </div>
        @else
            {{-- Period Closed Warning --}}
            @if($openingHours && !$openingHours->isPendaftaranBuka())
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 rounded-lg p-4">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-lock text-amber-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300 mb-1">Periode Input Data PKL Ditutup</p>
                            <p class="text-xs text-amber-700 dark:text-amber-400">Data Anda dapat dilihat tetapi tidak dapat diubah. Periode: {{ $openingHours->open_time?->format('d M Y') ?? '?' }} — {{ $openingHours->close_time?->format('d M Y') ?? '?' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Validation Errors Summary --}}
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30 rounded-lg p-4">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-red-800 dark:text-red-300 mb-1">Ditemukan Kesalahan Input</p>
                            <p class="text-xs text-red-700 dark:text-red-400">Silakan perbaiki kesalahan di bawah ini:</p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ $proposal ? route('mahasiswa.proposal.update') : route('mahasiswa.proposal.store') }}" enctype="multipart/form-data" class="space-y-6"
                  x-data="{
                      phoneInput: @js(old('hp_mentor', $proposal->hp_mentor ?? '')),
                      fileError: '',
                      fileName: '',
                      previewUrl: '',
                      showPreview: false,
                      sanitizePhone() {
                          // Only keep digits
                          let cleaned = this.phoneInput.replace(/[^0-9]/g, '');
                          // Ensure it starts with 0
                          if (cleaned.length > 0 && cleaned[0] !== '0') {
                              cleaned = '0' + cleaned;
                          }
                          // Limit to 13 digits
                          this.phoneInput = cleaned.substring(0, 13);
                      },
                      validateFile(event) {
                          const file = event.target.files[0];
                          this.fileError = '';
                          this.fileName = '';

                          // Revoke previous preview URL to free memory
                          if (this.previewUrl) {
                              URL.revokeObjectURL(this.previewUrl);
                              this.previewUrl = '';
                          }

                          if (!file) return;

                          this.fileName = file.name;

                          // Check both MIME type and file extension (some browsers may not provide MIME)
                          const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
                          if (!isPdf) {
                              this.fileError = 'File harus berformat PDF';
                              event.target.value = '';
                              this.fileName = '';
                              return;
                          }

                          const maxSize = 40 * 1024 * 1024; // 40MB
                          if (file.size > maxSize) {
                              this.fileError = 'Ukuran file maksimal 40MB';
                              event.target.value = '';
                              this.fileName = '';
                              return;
                          }

                          // Create preview URL
                          this.previewUrl = URL.createObjectURL(file);
                      },
                      openPreview() {
                          if (this.previewUrl) {
                              this.showPreview = true;
                          }
                      },
                      closePreview() {
                          this.showPreview = false;
                      }
                  }">
                @csrf
                @if($proposal) @method('PUT') @endif

                {{-- Section: Data Akademik --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-blue-500"></i>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Data Akademik</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">NIM / ID Pendaftar</label>
                            <input type="text" name="nim" value="{{ old('nim', $proposal->nim ?? $user->username) }}"
                                   maxlength="8"
                                   class="w-full px-3 py-2 border rounded-md bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-400 font-mono text-sm cursor-not-allowed @error('nim') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror"
                                   required readonly>
                            @error('nim')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="nama" value="{{ old('nama', $proposal->nama ?? $user->name) }}"
                                   maxlength="100"
                                   {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'disabled' : '' }}
                                   class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'cursor-not-allowed opacity-60' : '' }}"
                                   required>
                            @error('nama')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kode Lokal</label>
                            <input type="text" name="kd_lokal" value="{{ old('kd_lokal', $proposal->kd_lokal ?? $user->kd_lokal) }}"
                                   maxlength="15"
                                   class="w-full px-3 py-2 border rounded-md bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-400 font-mono uppercase text-sm cursor-not-allowed @error('kd_lokal') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror"
                                   readonly>
                            @error('kd_lokal')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jenis PKL</label>
                            <select name="jns_pkl"
                                    {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'disabled' : '' }}
                                    class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('jns_pkl') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'cursor-not-allowed opacity-60' : '' }}"
                                    required>
                                <option value="">Pilih Jenis</option>
                                <option value="Magang" {{ old('jns_pkl', $proposal->jns_pkl ?? $user->jenis ?? '') === 'Magang' ? 'selected' : '' }}>Magang Reguler</option>
                                <option value="Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)" {{ old('jns_pkl', $proposal->jns_pkl ?? $user->jenis ?? '') === 'Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)' ? 'selected' : '' }}>Program Merdeka (MSIB / MBKM)</option>
                            </select>
                            @if(!$proposal && $user->jenis)
                                <p class="mt-1 text-xs text-blue-600 dark:text-blue-400">
                                    <i class="fa-solid fa-info-circle text-[10px] mr-1"></i>
                                    Default dari admin: <strong>{{ $user->jenis === 'Magang' ? 'Magang Reguler' : 'Program Magang Khusus' }}</strong>. Anda dapat mengubahnya jika perlu.
                                </p>
                            @endif
                            @error('jns_pkl')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Dosen Pembimbing Akademik</label>
                            <div class="relative">
                                <i class="fa-solid fa-chalkboard-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" value="{{ $user->dosenPaLabel() }}"
                                       class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-400 text-sm cursor-not-allowed"
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section: Data Institusi --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <i class="fa-solid fa-building text-blue-500"></i>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Data Institusi & Mentor</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Instansi / Tempat PKL</label>
                            <input type="text" name="tempat_riset" value="{{ old('tempat_riset', $proposal->tempat_riset ?? '') }}"
                                   maxlength="100"
                                   {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'disabled' : '' }}
                                   class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tempat_riset') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'cursor-not-allowed opacity-60' : '' }}"
                                   placeholder="Contoh: PT. Bank Central Asia Tbk"
                                   required>
                            @error('tempat_riset')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judul Laporan PKL</label>
                            <input type="text" name="judul_pkl" value="{{ old('judul_pkl', $proposal->judul_pkl ?? '') }}"
                                   maxlength="255"
                                   {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'disabled' : '' }}
                                   class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('judul_pkl') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'cursor-not-allowed opacity-60' : '' }}"
                                   placeholder="Contoh: Analisis Sistem Jaringan pada..."
                                   required>
                            @error('judul_pkl')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Mentor Industri</label>
                            <input type="text" name="nama_mentor" value="{{ old('nama_mentor', $proposal->nama_mentor ?? '') }}"
                                   maxlength="100"
                                   {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'disabled' : '' }}
                                   class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nama_mentor') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'cursor-not-allowed opacity-60' : '' }}"
                                   required>
                            @error('nama_mentor')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nomor Ponsel Mentor</label>
                            <input type="text" name="hp_mentor" x-model="phoneInput" @input="sanitizePhone"
                                   maxlength="13" inputmode="numeric" autocomplete="tel" pattern="^0[0-9]{9,12}$"
                                   {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'disabled' : '' }}
                                   class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white font-mono text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('hp_mentor') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'cursor-not-allowed opacity-60' : '' }}"
                                   placeholder="081234567890"
                                   required>
                            @error('hp_mentor')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @else
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Harus diawali 0, hanya angka, 10-13 digit (contoh: 081234567890)</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Mentor (Aktif)</label>
                            <input type="email" name="email_mentor" value="{{ old('email_mentor', $proposal->email_mentor ?? '') }}"
                                   maxlength="100"
                                   {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'disabled' : '' }}
                                   class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email_mentor') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'cursor-not-allowed opacity-60' : '' }}"
                                   required>
                            @error('email_mentor')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Instansi</label>
                            <input type="email" name="email_perusahaan" value="{{ old('email_perusahaan', $proposal->email_perusahaan ?? '') }}"
                                   maxlength="100"
                                   {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'disabled' : '' }}
                                   class="w-full px-3 py-2 border rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email_perusahaan') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'cursor-not-allowed opacity-60' : '' }}">
                            @error('email_perusahaan')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Surat Keterangan Magang --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf text-rose-500"></i>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Surat Keterangan Magang (SKM)</h2>
                    </div>
                    <div class="p-6">
                        <label for="skm-upload"
                               class="flex flex-col items-center justify-center w-full py-10 px-4 border-2 border-dashed rounded-md {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }} transition-colors @error('skm') border-red-500 hover:border-red-600 @else border-gray-300 dark:border-gray-600 {{ $openingHours && !$openingHours->isPendaftaranBuka() ? '' : 'hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:border-blue-300 dark:hover:border-blue-700' }} @enderror">
                            <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-400 mb-2"></i>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="fileName || '{{ $proposal && $proposal->skm ? 'Klik untuk ganti file' : 'Klik untuk pilih file PDF' }}'"></span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format PDF, maksimal 40 MB</span>
                        </label>
                        <input id="skm-upload" name="skm" type="file" class="sr-only fixed" accept="application/pdf,.pdf"
                               {{ $openingHours && !$openingHours->isPendaftaranBuka() ? 'disabled' : '' }}
                               @change="validateFile($event)"
                               {{ !$proposal ? 'required' : '' }}>

                        {{-- Preview button for newly selected file --}}
                        <div x-show="fileName && previewUrl" x-cloak class="mt-3">
                            <button type="button" @click="openPreview()"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                                <i class="fa-solid fa-eye"></i>
                                Preview PDF
                            </button>
                        </div>

                        @if($proposal && $proposal->skm)
                            <div class="mt-3 flex items-center gap-2 px-3 py-2 rounded-md bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/30">
                                <i class="fa-solid fa-circle-check text-emerald-500 shrink-0"></i>
                                <p class="text-xs font-medium text-emerald-700 dark:text-emerald-400 truncate">Berkas saat ini: {{ $proposal->skm }}</p>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('files.serve', encryptUrl($proposal->skm)) }}" target="_blank"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800/30 text-emerald-700 dark:text-emerald-400 text-sm font-medium transition-colors">
                                    <i class="fa-solid fa-external-link-alt"></i>
                                    Lihat Berkas
                                </a>
                            </div>
                        @endif

                        {{-- Frontend file validation error --}}
                        <div x-show="fileError" x-cloak class="mt-3 flex items-center gap-2 px-3 py-2 rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30">
                            <i class="fa-solid fa-triangle-exclamation text-red-500 shrink-0"></i>
                            <p class="text-xs font-medium text-red-700 dark:text-red-400" x-text="fileError"></p>
                        </div>

                        {{-- Backend validation error --}}
                        @error('skm')
                            <div class="mt-3 flex items-center gap-2 px-3 py-2 rounded-md bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30">
                                <i class="fa-solid fa-triangle-exclamation text-red-500 shrink-0"></i>
                                <p class="text-xs font-medium text-red-700 dark:text-red-400">{{ $message }}</p>
                            </div>
                            <p class="mt-2 text-xs text-gray-600 dark:text-gray-400 italic">
                                <i class="fa-solid fa-info-circle mr-1"></i>
                                Pilih ulang file SKM setelah validasi gagal.
                            </p>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i>
                        Pastikan data diisi dengan valid dan sesuai SK.
                    </p>
                    @if($openingHours && !$openingHours->isPendaftaranBuka())
                        <button type="button" disabled
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-md bg-gray-400 text-white text-sm font-medium cursor-not-allowed opacity-60">
                            <i class="fa-solid fa-lock"></i>
                            Periode Ditutup
                        </button>
                    @else
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            {{ $proposal ? 'Simpan Perubahan' : 'Ajukan Proposal' }}
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
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Preview PDF</h3>
                            <button type="button" @click="closePreview()"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                <i class="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        {{-- Modal Body --}}
                        <div class="flex-1 overflow-hidden">
                            <iframe :src="previewUrl" class="w-full h-full border-0"></iframe>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</x-app-layout>
