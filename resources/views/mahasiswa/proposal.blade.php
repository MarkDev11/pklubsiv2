<x-app-layout>
    <x-slot name="title">Form Data PKL</x-slot>

    <div class="space-y-6 animate-fade-in pb-12">
        
        {{-- Premium Header --}}
        <div class="relative overflow-hidden rounded-[2rem] p-8 border border-white/20 shadow-lg bg-gradient-to-r from-indigo-900 via-blue-900 to-sky-900 isolate">
            <div class="absolute -top-12 -right-12 w-64 h-64 bg-blue-500 rounded-full mix-blend-multiply filter blur-[60px] opacity-60 animate-pulse"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 shadow-inner flex items-center justify-center backdrop-blur-md">
                        <i class="fa-solid fa-file-signature text-2xl text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-3xl font-black text-white tracking-tight">
                            {{ $proposal ? 'Perbarui Data PKL' : 'Pendaftaran Data PKL' }}
                        </h2>
                        <p class="text-sky-100/80 font-medium text-sm mt-1">Lengkapi informasi akademik dan institusi pelaksana PKL Anda.</p>
                    </div>
                </div>
                <div class="hidden sm:block">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn-secondary !rounded-xl !px-4 !py-2 !text-sm !font-bold backdrop-blur-md bg-white/10 text-white border-white/20 hover:bg-white/20">
                        <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        @if($openingHours && !$openingHours->isPendaftaranBuka() && !$proposal)
            {{-- Error State: Closed --}}
            <div class="bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30 rounded-[2rem] p-10 text-center flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-red-100 dark:bg-red-900/20 text-red-500 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-lock text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-red-700 dark:text-red-400 mb-2">Masa Pengisian Data Ditutup</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-sm mx-auto">
                    Periode resmi berdasarkan konfigurasi sistem: <br>
                    <span class="font-bold text-gray-800 dark:text-gray-200 mt-2 block">
                        {{ $openingHours->open_time?->format('d M Y') ?? '?' }} — {{ $openingHours->close_time?->format('d M Y') ?? '?' }}
                    </span>
                </p>
                <a href="{{ route('mahasiswa.dashboard') }}" class="mt-6 px-6 py-2.5 rounded-xl bg-white dark:bg-surface-800 border border-gray-200 dark:border-surface-700 text-sm font-bold shadow-sm hover:bg-gray-50 dark:hover:bg-surface-700 transition-colors">Kembali ke Dashboard</a>
            </div>
        @elseif($proposal && $proposal->nilai !== null)
            {{-- Error State: Graded --}}
            <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/30 rounded-[2rem] p-10 text-center flex flex-col items-center">
                <div class="w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/20 text-blue-500 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-medal text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-blue-700 dark:text-blue-400 mb-2">Penilaian Selesai</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                    Selamat! PKL Anda telah dinilai. Seluruh data dasar pengajuan Anda telah dikunci secara permanen dan <strong>tidak dapat diubah lagi</strong> untuk menjaga integritas laporan akhir.
                </p>
                <div class="mt-6 flex gap-4">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="px-6 py-2.5 rounded-xl bg-blue-600 border border-blue-500 text-white text-sm font-bold shadow-[0_0_20px_rgba(59,130,246,0.3)] hover:bg-blue-500 transition-colors">Cek Nilai di Dashboard</a>
                </div>
            </div>
        @else
            {{-- Premium Form Card --}}
            <div class="bg-white dark:bg-surface-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-surface-700 overflow-hidden">
                <form method="POST" action="{{ $proposal ? route('mahasiswa.proposal.update') : route('mahasiswa.proposal.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if($proposal) @method('PUT') @endif
                    
                    <div class="grid grid-cols-1 lg:grid-cols-5 divide-y lg:divide-y-0 lg:divide-x divide-gray-100 dark:divide-surface-700">
                        
                        {{-- Left Column: Data Mahasiswa & Dasar --}}
                        <div class="p-8 lg:col-span-2 bg-gray-50/50 dark:bg-surface-850/20">
                            <h3 class="font-bold text-gray-900 dark:text-white text-base mb-6 flex items-center gap-2">
                                <i class="fa-solid fa-id-card text-indigo-500"></i> Data Akademik
                            </h3>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="form-label text-xs tracking-wider uppercase text-gray-500">NIM / ID Pendaftar</label>
                                    <input type="text" name="nim" value="{{ old('nim', $proposal->nim ?? $user->username) }}" class="form-input bg-gray-100 dark:bg-surface-900 border-transparent font-mono font-bold text-indigo-600 dark:text-indigo-400 cursor-not-allowed" required readonly>
                                </div>
                                <div>
                                    <label class="form-label text-xs tracking-wider uppercase text-gray-500">Nama Lengkap</label>
                                    <input type="text" name="nama" value="{{ old('nama', $proposal->nama ?? $user->name) }}" class="form-input font-medium" required>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="form-label text-xs tracking-wider uppercase text-gray-500">Kode Lokal</label>
                                        <input type="text" name="kd_lokal" value="{{ old('kd_lokal', $proposal->kd_lokal ?? $user->kd_lokal) }}" class="form-input font-mono uppercase bg-gray-50 dark:bg-surface-900 cursor-not-allowed" readonly>
                                    </div>
                                    <div>
                                        <label class="form-label text-xs tracking-wider uppercase text-gray-500">Jenis PKL</label>
                                        <select name="jns_pkl" class="form-select font-medium" required>
                                            <option value="">Pilih Jenis</option>
                                            <option value="Magang" {{ old('jns_pkl', $proposal->jns_pkl ?? '') === 'Magang' ? 'selected' : '' }}>Magang Reguler</option>
                                            <option value="Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)" {{ old('jns_pkl', $proposal->jns_pkl ?? '') === 'Program Magang khusus (PMK/GNIK/MBKM/MSIB/PMMB)' ? 'selected' : '' }}>Program Merdeka (MSIB/MBKM)</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label text-xs tracking-wider uppercase text-gray-500">Dosen Pembimbing Akademik</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-chalkboard-user text-gray-400"></i>
                                        </div>
                                        <input type="text" value="{{ $user->nama_dosen_pa ?? '-' }}" class="form-input pl-10 bg-gray-100 dark:bg-surface-900 border-transparent text-gray-600 dark:text-gray-400 cursor-not-allowed" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Data Institusi & Upload --}}
                        <div class="p-8 lg:col-span-3">
                            <h3 class="font-bold text-gray-900 dark:text-white text-base mb-6 flex items-center gap-2">
                                <i class="fa-solid fa-building-user text-indigo-500"></i> Informasi Institusi & Mentor
                            </h3>
                            
                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="md:col-span-2">
                                        <label class="form-label text-xs tracking-wider uppercase text-gray-500">Nama Instansi / Tempat Kedudukan PKL</label>
                                        <input type="text" name="tempat_riset" value="{{ old('tempat_riset', $proposal->tempat_riset ?? '') }}" class="form-input font-medium" placeholder="Contoh: PT. Bank Central Asia Tbk" required>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="form-label text-xs tracking-wider uppercase text-gray-500">Judul Laporan PKL</label>
                                        <input type="text" name="judul_pkl" value="{{ old('judul_pkl', $proposal->judul_pkl ?? '') }}" class="form-input font-medium" placeholder="Contoh: Analisis Sistem Jaringan pada..." required>
                                    </div>
                                    
                                    <div class="md:col-span-2 pt-2">
                                        <hr class="border-gray-100 dark:border-surface-700 mb-4">
                                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Data Kepenasihatan Eksternal</h4>
                                    </div>
                                    
                                    <div>
                                        <label class="form-label text-xs tracking-wider uppercase text-gray-500">Nama Mentor Industri</label>
                                        <input type="text" name="nama_mentor" value="{{ old('nama_mentor', $proposal->nama_mentor ?? '') }}" class="form-input font-medium" required>
                                    </div>
                                    <div>
                                        <label class="form-label text-xs tracking-wider uppercase text-gray-500">Nomor Ponsel Mentor</label>
                                        <input type="text" name="hp_mentor" value="{{ old('hp_mentor', $proposal->hp_mentor ?? '') }}" class="form-input font-medium font-mono" placeholder="08..." required>
                                    </div>
                                    <div>
                                        <label class="form-label text-xs tracking-wider uppercase text-gray-500">Email Mentor (Aktif)</label>
                                        <input type="email" name="email_mentor" value="{{ old('email_mentor', $proposal->email_mentor ?? '') }}" class="form-input font-medium" required>
                                    </div>
                                    <div>
                                        <label class="form-label text-xs tracking-wider uppercase text-gray-500">Email Instansi</label>
                                        <input type="email" name="email_perusahaan" value="{{ old('email_perusahaan', $proposal->email_perusahaan ?? '') }}" class="form-input font-medium">
                                    </div>

                                    <div class="md:col-span-2 pt-2">
                                        <hr class="border-gray-100 dark:border-surface-700 mb-4">
                                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Dokumentasi Finalisasi</h4>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="form-label text-xs tracking-wider uppercase text-gray-500">Surat Keterangan Magang (SKM)</label>
                                        <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-surface-600 border-dashed rounded-2xl hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors relative group">
                                            <div class="space-y-2 text-center relative z-10 w-full relative">
                                                <i class="fa-solid fa-file-pdf text-3xl text-red-500 mb-2"></i>
                                                <div class="flex flex-col sm:flex-row text-sm text-gray-600 dark:text-gray-400 justify-center items-center gap-1">
                                                    <label for="skm-upload" class="relative cursor-pointer rounded-md bg-white dark:bg-surface-800 font-bold text-indigo-600 dark:text-indigo-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500 px-2 py-1 shadow-sm border border-gray-200 dark:border-surface-600">
                                                        <span>Unggah File Disini</span>
                                                        <input id="skm-upload" name="skm" type="file" class="sr-only" accept=".pdf" {{ !$proposal ? 'required' : '' }}>
                                                    </label>
                                                    <p class="pl-1">atau seret (drag and drop)</p>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-500">Format PDF hingga maksimal 40MB</p>
                                            </div>
                                        </div>
                                        @if($proposal && $proposal->skm)
                                        <div class="mt-3 flex items-center gap-2 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/30">
                                            <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                            <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 truncate w-full">Berkas saat ini: {{ $proposal->skm }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Footer Actions --}}
                    <div class="bg-gray-50/50 dark:bg-surface-800/50 border-t border-gray-100 dark:border-surface-700 px-8 py-5 flex items-center justify-between flex-wrap gap-4">
                        <div class="text-xs text-gray-500 font-medium">
                            Pastikan data diisi dengan valid dan sesuai SK.
                        </div>
                        <button type="submit" class="btn-primary inline-flex items-center gap-2 shadow-[0_0_15px_rgba(79,70,229,0.3)] hover:shadow-[0_0_25px_rgba(79,70,229,0.5)] transition-shadow">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            {{ $proposal ? 'Simpan Perubahan' : 'Ajukan Proposal' }}
                        </button>
                    </div>
                </form>
            </div>
            
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/10 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 mt-0.5"></i>
                        <div>
                            <h3 class="text-sm font-bold text-red-800 dark:text-red-400 mb-1">Ditemukan Kesalahan Input</h3>
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
</x-app-layout>
