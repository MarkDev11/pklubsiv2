<x-app-layout>
    <x-slot name="title">Dashboard Dosen PA</x-slot>

    <div class="space-y-8 animate-fade-in pb-10">

        {{-- Welcome Banner - Premium Purple/Cyan Glassmorphism --}}
        <div class="relative overflow-hidden rounded-[2rem] p-8 lg:p-10 border border-white/20 shadow-2xl bg-gradient-to-br from-purple-900 via-fuchsia-900 to-cyan-900 isolate">
            {{-- Background decorative shapes --}}
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-fuchsia-500 rounded-full mix-blend-multiply filter blur-[80px] opacity-60 animate-pulse"></div>
            <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-cyan-500 rounded-full mix-blend-multiply filter blur-[80px] opacity-60 animate-pulse" style="animation-delay: 2s;"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    {{-- Avatar/Greeting Icon --}}
                    <div class="w-20 h-20 rounded-2xl bg-white/10 border border-white/20 shadow-[0_0_40px_rgba(255,255,255,0.1)] flex items-center justify-center backdrop-blur-xl shrink-0 group hover:scale-105 transition-transform duration-500">
                        <i class="fa-solid fa-chalkboard-user text-3xl text-white group-hover:text-fuchsia-300 transition-colors"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-white to-fuchsia-200 tracking-tight">
                            Halo, {{ collect(explode(' ', $user->name))->reject(fn($n) => str_ends_with($n, '.') || strlen($n) <= 2)->first() ?? $user->name }}!
                        </h2>
                        <p class="text-fuchsia-100/80 font-medium text-sm md:text-base mt-1.5 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_rgba(34,211,238,0.8)]"></span>
                            Dosen Pembimbing Akademik
                        </p>
                    </div>
                </div>
                
                {{-- Quick Header Actions --}}
                <div class="flex gap-3 mt-4 md:mt-0">
                    <button x-data x-on:click="$dispatch('open-modal', 'list-mahasiswa')" class="group relative px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-sm transition-all duration-300 backdrop-blur-md overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-[150%] xl:group-hover:animate-[shimmer_1.5s_infinite]"></div>
                        <span class="flex items-center gap-2 relative z-10">
                            <i class="fa-solid fa-list-check text-lg"></i> Mahasiswa Bimbingan
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Interactive Deadline Alert --}}
        @if($deadline && $deadline->open_nilai)
            @php
                $now = now();
                $closeNilai = \Carbon\Carbon::parse($deadline->close_nilai);
                $openNilai = \Carbon\Carbon::parse($deadline->open_nilai);
            @endphp
            @if($now->gt($closeNilai))
                <div class="bg-red-50 dark:bg-red-900/10 border-l-4 border-red-500 rounded-r-xl p-4 shadow-sm flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/20 text-red-600 flex items-center justify-center shrink-0 mt-0.5"><i class="fa-solid fa-lock text-lg"></i></div>
                    <div>
                        <h4 class="font-bold text-red-800 dark:text-red-400">Portal Penilaian Ditutup</h4>
                        <p class="text-sm text-red-600 dark:text-red-300 mt-1">Sesi pengisian formulir nilai tugas akhir PKL dan MSIB telah ditutup dari pusat (Administrator).</p>
                    </div>
                </div>
            @elseif($now->gte($openNilai))
                <div class="bg-cyan-50 dark:bg-cyan-900/10 border-l-4 border-cyan-500 rounded-r-xl p-4 shadow-sm flex items-start gap-4 ring-1 ring-cyan-100 dark:ring-cyan-800/30">
                    <div class="w-10 h-10 rounded-full bg-cyan-100 dark:bg-cyan-900/20 text-cyan-600 flex items-center justify-center shrink-0 mt-0.5"><i class="fa-solid fa-bell text-lg animate-pulse"></i></div>
                    <div class="w-full">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-cyan-800 dark:text-cyan-400">Tugas Penilaian Dibuka</h4>
                            <span class="px-2.5 py-1 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 text-xs font-bold rounded-md">Sisa {{ intval($now->diffInDays($closeNilai)) }} Hari</span>
                        </div>
                        <p class="text-sm text-cyan-600 dark:text-cyan-300 mt-1 text-justify md:text-left">Diharapkan seluruh Dosen PA memvalidasi dokumen Laporan PKL/MSIB dan menyelesaiakan form penilaian selambatnya <strong>{{ $closeNilai->format('d M Y') }}</strong>.</p>
                    </div>
                </div>
            @endif
        @endif

        {{-- Stats Grid - Academic Focus Focus (Purple/Cyan scheme) --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 stagger-fade">
            @php
                $stats = [
                    [
                        'label' => 'Total Bimbingan', 'value' => $jumlahMahasiswa, 'icon' => 'fa-users', 
                        'glow' => 'bg-fuchsia-500/10 dark:bg-fuchsia-400/10',
                        'icon_bg' => 'bg-fuchsia-50 dark:bg-fuchsia-500/10 group-hover:bg-fuchsia-500',
                        'icon_text' => 'text-fuchsia-500 dark:text-fuchsia-400 group-hover:text-white',
                        'icon_border' => 'border-fuchsia-100 dark:border-fuchsia-500/20',
                        'val_hover' => 'group-hover:text-fuchsia-500 dark:group-hover:text-fuchsia-400',
                    ],
                    [
                        'label' => 'Validasi Selesai', 'value' => $sudahDinilai, 'icon' => 'fa-check-double', 
                        'glow' => 'bg-emerald-500/10 dark:bg-emerald-400/10',
                        'icon_bg' => 'bg-emerald-50 dark:bg-emerald-500/10 group-hover:bg-emerald-500',
                        'icon_text' => 'text-emerald-500 dark:text-emerald-400 group-hover:text-white',
                        'icon_border' => 'border-emerald-100 dark:border-emerald-500/20',
                        'val_hover' => 'group-hover:text-emerald-500 dark:group-hover:text-emerald-400',
                    ],
                    [
                        'label' => 'Penilaian Tertunda', 'value' => $belumDinilai, 'icon' => 'fa-clipboard-question', 
                        'glow' => 'bg-amber-500/10 dark:bg-amber-400/10',
                        'icon_bg' => 'bg-amber-50 dark:bg-amber-500/10 group-hover:bg-amber-500',
                        'icon_text' => 'text-amber-500 dark:text-amber-400 group-hover:text-white',
                        'icon_border' => 'border-amber-100 dark:border-amber-500/20',
                        'val_hover' => 'group-hover:text-amber-500 dark:group-hover:text-amber-400',
                    ],
                    [
                        'label' => 'Mhs Belum Input', 'value' => $belumInputForm, 'icon' => 'fa-user-clock', 
                        'glow' => 'bg-rose-500/10 dark:bg-rose-400/10',
                        'icon_bg' => 'bg-rose-50 dark:bg-rose-500/10 group-hover:bg-rose-500',
                        'icon_text' => 'text-rose-500 dark:text-rose-400 group-hover:text-white',
                        'icon_border' => 'border-rose-100 dark:border-rose-500/20',
                        'val_hover' => 'group-hover:text-rose-500 dark:group-hover:text-rose-400',
                    ],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="bg-white/80 dark:bg-surface-800/80 backdrop-blur-xl border border-gray-200/50 dark:border-surface-700/50 p-6 sm:p-8 rounded-[1.5rem] shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-24 h-24 {{ $stat['glow'] }} rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="flex items-center gap-4 relative z-10">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center {{ $stat['icon_bg'] }} {{ $stat['icon_text'] }} shadow-inner border {{ $stat['icon_border'] }} transition-colors duration-300">
                        <i class="fa-solid {{ $stat['icon'] }} text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white tracking-tight mt-1 {{ $stat['val_hover'] }} transition-colors">{{ $stat['value'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Core Utilities Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Fast Navigator 1: Data Mhs --}}
            <div class="bg-white dark:bg-surface-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-surface-700 hover:border-fuchsia-300 dark:hover:border-fuchsia-700 transition-colors">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/20 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-users-viewfinder text-xl"></i></div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Pusat Data Mahasiswa</h3>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('dosen.mahasiswa.pkl') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-surface-700 group transition-colors border border-transparent hover:border-gray-200 dark:hover:border-surface-600">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-purple-600">Data Mahasiswa PKL</span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-purple-500"></i>
                    </a>
                    <a href="{{ route('dosen.mahasiswa.msib') }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-surface-700 group transition-colors border border-transparent hover:border-gray-200 dark:hover:border-surface-600">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 group-hover:text-purple-600">Data Mahasiswa MSIB</span>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-purple-500"></i>
                    </a>
                </div>
            </div>

            {{-- Fast Navigator 2: Grading/Penilaian --}}
            <div class="bg-white dark:bg-surface-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-surface-700 hover:border-cyan-300 dark:hover:border-cyan-700 transition-colors">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-12 h-12 rounded-xl bg-cyan-50 dark:bg-cyan-900/20 text-cyan-600 flex items-center justify-center"><i class="fa-solid fa-star-half-stroke text-xl"></i></div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Eksekutor Penilaian Akhir</h3>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('dosen.nilai.pkl') }}" class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-surface-700 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 hover:border-cyan-200 dark:hover:border-cyan-700/50 border border-gray-200 dark:border-surface-600 group transition-all">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-clipboard-check text-gray-400 group-hover:text-cyan-500 text-lg"></i>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 group-hover:text-cyan-700 dark:group-hover:text-cyan-400">Verifikasi Nilai PKL</h4>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-0.5">Input Nomerik & Evaluasi PDF</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-cyan-500"></i>
                    </a>
                    <a href="{{ route('dosen.nilai.msib') }}" class="flex items-center justify-between p-4 rounded-xl bg-gray-50 dark:bg-surface-700 hover:bg-cyan-50 dark:hover:bg-cyan-900/20 hover:border-cyan-200 dark:hover:border-cyan-700/50 border border-gray-200 dark:border-surface-600 group transition-all">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-medal text-gray-400 group-hover:text-cyan-500 text-lg"></i>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 group-hover:text-cyan-700 dark:group-hover:text-cyan-400">Konversi Nilai MSIB</h4>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-0.5">Transfer Mutu Sertifikat</p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 group-hover:text-cyan-500"></i>
                    </a>
                </div>
            </div>

            {{-- Fast Navigator 3: Reporting & PDF --}}
            <div class="bg-white dark:bg-surface-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-surface-700 relative overflow-hidden group/pdf">
                <div class="absolute -right-12 -top-12 opacity-5 pointer-events-none transition-transform duration-500 group-hover/pdf:scale-110">
                    <i class="fa-solid fa-file-pdf text-9xl"></i>
                </div>
                <div class="flex items-center gap-3 mb-6 relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 flex items-center justify-center"><i class="fa-solid fa-print text-xl"></i></div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Pelaporan Dokumen PDF</h3>
                </div>
                
                <div class="space-y-4 relative z-10 flex flex-col h-[calc(100%-80px)] justify-end">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 leading-relaxed">Sistem mencetak seluruh metadata penilaian mahasiswa ke dalam Berita Acara yang tersentralisasi.</p>
                    
                    <a href="{{ route('dosen.pdf.pkl') }}" target="_blank" class="w-full btn-primary !justify-center shadow-[0_0_15px_rgba(79,70,229,0.3)] hover:shadow-[0_0_25px_rgba(79,70,229,0.5)]">
                        <i class="fa-solid fa-file-pdf mr-1.5"></i> Cetak Berita Acara PKL
                    </a>
                    <a href="{{ route('dosen.pdf.msib') }}" target="_blank" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-surface-600 hover:border-gray-300 dark:hover:border-surface-500 bg-white dark:bg-surface-800 text-gray-700 dark:text-gray-200 font-bold text-sm text-center shadow-sm transition-all hover:bg-gray-50 dark:hover:bg-surface-700">
                        <i class="fa-solid fa-file-pdf mr-1.5 text-rose-500"></i> Cetak Dokumen MSIB
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal List Mahasiswa (Sleek List) --}}
    <div x-data="{ open: false }"
         x-on:open-modal.window="if($event.detail === 'list-mahasiswa') open = true"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition.opacity duration.200ms
             @click="open = false" class="fixed inset-0 bg-gray-900/60 dark:bg-black/60 backdrop-blur-sm"></div>
        
        <div x-show="open" x-transition.scale.95.opacity duration.300ms
             class="relative bg-white dark:bg-surface-800 rounded-[2rem] shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-hidden flex flex-col z-10 border border-gray-100 dark:border-surface-700">
            
            <div class="px-6 py-5 border-b border-gray-100 dark:border-surface-700 flex justify-between items-center bg-gray-50/50 dark:bg-surface-850/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-fuchsia-500 to-purple-600 rounded-xl flex items-center justify-center shadow-sm text-white">
                        <i class="fa-solid fa-clipboard-user text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Mahasiswa Bimbingan</h3>
                        <p class="text-[10px] uppercase font-bold tracking-widest text-gray-500">Anda membimbing {{ $listMahasiswa->count() }} orang</p>
                    </div>
                </div>
                <button @click="open = false" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-surface-600 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="overflow-y-auto p-4 custom-scrollbar">
                @if(isset($listMahasiswa) && $listMahasiswa->count())
                    <div class="space-y-3">
                        @foreach($listMahasiswa as $i => $m)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 rounded-2xl border border-gray-100 dark:border-surface-700 bg-white dark:bg-surface-800 hover:shadow-md hover:border-gray-200 dark:hover:border-surface-600 transition-all gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-surface-700 text-gray-500 font-bold flex items-center justify-center text-xs border border-gray-200 dark:border-surface-600">{{ $i+1 }}</div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $m->name }}</p>
                                    <p class="text-xs font-mono text-indigo-500 dark:text-indigo-400 mt-0.5"><i class="fa-regular fa-id-card mr-1"></i> {{ $m->username }}</p>
                                </div>
                            </div>
                            <div class="shrink-0">
                                @if(Str::contains(strtolower($m->jenis ?? ''), 'msib'))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 text-xs font-bold uppercase tracking-wider border border-emerald-100 dark:border-emerald-800/30">
                                        <i class="fa-solid fa-rocket"></i> MSIB
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-50 dark:bg-sky-900/20 text-sky-600 dark:text-sky-400 text-xs font-bold uppercase tracking-wider border border-sky-100 dark:border-sky-800/30">
                                        <i class="fa-solid fa-briefcase"></i> Magang PKL
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-gray-50 dark:bg-surface-700 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-ghost text-2xl text-gray-400"></i>
                        </div>
                        <p class="font-bold text-gray-900 dark:text-white">Tidak Ada Anak Bimbing</p>
                        <p class="text-sm text-gray-500 mt-1 max-w-sm">Administrator belum mengalokasikan satupun mahasiswa di bawah pertanggungjawaban Anda.</p>
                    </div>
                @endif
            </div>
            
        </div>
    </div>
</x-app-layout>
