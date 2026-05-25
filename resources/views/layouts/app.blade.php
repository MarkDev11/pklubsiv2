<!DOCTYPE html>
<html lang="id"
      x-data="{
          darkMode: localStorage.getItem('darkMode') === 'true',
          sidebarOpen: window.innerWidth >= 1024
      }"
      x-init="
          $watch('darkMode', val => localStorage.setItem('darkMode', val));
          window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
              if (localStorage.getItem('darkMode') === null) darkMode = e.matches;
          });
      "
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Informasi Praktik Kerja Lapangan - Universitas BSI">

    <title>{{ $title ?? 'Dashboard' }} — Sistem PKL UBSI</title>
    
    <link rel="icon" type="image/png" href="{{ asset('images/pkl_logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-surface-950 text-gray-900 dark:text-gray-100 min-h-screen">

    {{-- Sidebar Overlay (mobile) --}}
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    {{-- ============================================
         SIDEBAR — Blue Gradient
         ============================================ --}}
    <aside class="sidebar"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           @click.away="if(window.innerWidth < 1024) sidebarOpen = false">

        {{-- Sidebar Header / Logo --}}
        <div class="h-16 flex items-center gap-3 px-5 shrink-0 border-b border-white/10 dark:border-surface-700/50 relative overflow-hidden bg-white/5">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-indigo-600/20 pointer-events-none"></div>
            <div class="w-10 h-10 rounded-lg flex items-center justify-center shadow-lg relative z-10 overflow-hidden border border-white/20 shrink-0">
                <img src="{{ asset('images/pkl_logo.png') }}" alt="Logo PKL BSI" class="w-full h-full object-fill scale-[1.1]">
            </div>
            <div class="relative z-10">
                <h1 class="text-sm font-black text-white tracking-wider leading-tight">Sistem PKL</h1>
                <p class="text-[10px] text-blue-200/80 font-bold tracking-widest uppercase">Univ. BSI</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-6 space-y-1 overflow-y-auto custom-scrollbar">
            @php $role = auth()->user()->role->value; @endphp

            {{-- Dashboard --}}
            <a href="{{ route($role . '.dashboard') }}"
               class="sidebar-link {{ request()->routeIs($role . '.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie w-5 text-center text-lg"></i>
                <span>Dashboard</span>
            </a>

            @if($role === 'admin')
                <div class="pt-5 pb-2">
                    <p class="text-[10px] font-black tracking-widest text-blue-200/50 uppercase pl-3">Kelola Sistem</p>
                </div>

                <a href="{{ route('admin.akun.index') }}" class="sidebar-link {{ request()->routeIs('admin.akun.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear w-5 text-center text-lg"></i>
                    <span>Daftar Akun</span>
                </a>

                <a href="{{ route('admin.import.index') }}" class="sidebar-link {{ request()->routeIs('admin.import.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-import w-5 text-center text-lg"></i>
                    <span>Import Data</span>
                </a>

                <a href="{{ route('admin.mahasiswa.index') }}" class="sidebar-link {{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-id-card-clip w-5 text-center text-lg"></i>
                    <span>Data Mahasiswa</span>
                </a>

                <a href="{{ route('admin.tanggal.index') }}" class="sidebar-link {{ request()->routeIs('admin.tanggal.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check w-5 text-center text-lg"></i>
                    <span>Pengaturan Tanggal</span>
                </a>

                <div class="pt-5 pb-2">
                    <p class="text-[10px] font-black tracking-widest text-blue-200/50 uppercase pl-3">Penilaian Akhir</p>
                </div>

                <a href="{{ route('admin.nilai.pkl') }}" class="sidebar-link {{ request()->routeIs('admin.nilai.pkl') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-signature w-5 text-center text-lg"></i>
                    <span>Nilai PKL Magang</span>
                </a>

                <a href="{{ route('admin.nilai.msib') }}" class="sidebar-link {{ request()->routeIs('admin.nilai.msib') ? 'active' : '' }}">
                    <i class="fa-solid fa-briefcase w-5 text-center text-lg"></i>
                    <span>Nilai MSIB / PMK</span>
                </a>

                <div class="pt-5 pb-2">
                    <p class="text-[10px] font-black tracking-widest text-blue-200/50 uppercase pl-3">Cetak Berkas</p>
                </div>
                <a href="{{ route('admin.pdf.pkl') }}" target="_blank" class="sidebar-link">
                    <i class="fa-solid fa-file-pdf w-5 text-center text-lg text-red-300"></i>
                    <span>PDF Nilai PKL</span>
                </a>
                <a href="{{ route('admin.pdf.msib') }}" target="_blank" class="sidebar-link">
                    <i class="fa-solid fa-file-pdf w-5 text-center text-lg text-red-300"></i>
                    <span>PDF Nilai MSIB</span>
                </a>
            @endif

            @if($role === 'mahasiswa')
                <div class="pt-5 pb-2">
                    <p class="text-[10px] font-black tracking-widest text-blue-200/50 uppercase pl-3">Dokumen PKL</p>
                </div>

                <a href="{{ route('mahasiswa.proposal.index') }}" class="sidebar-link {{ request()->routeIs('mahasiswa.proposal.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-folder-open w-5 text-center text-lg"></i>
                    <span>Data PKL / Surat</span>
                </a>

                <a href="{{ route('mahasiswa.laporan.index') }}" class="sidebar-link {{ request()->routeIs('mahasiswa.laporan.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cloud-arrow-up w-5 text-center text-lg"></i>
                    <span>Upload Laporan</span>
                </a>
            @endif

            @if($role === 'dosen')
                <div class="pt-5 pb-2">
                    <p class="text-[10px] font-black tracking-widest text-blue-200/50 uppercase pl-3">Bimbingan</p>
                </div>

                <a href="{{ route('dosen.mahasiswa.pkl') }}" class="sidebar-link {{ request()->routeIs('dosen.mahasiswa.pkl') ? 'active' : '' }}">
                    <i class="fa-solid fa-users w-5 text-center text-lg"></i>
                    <span>Mahasiswa PKL</span>
                </a>
                <a href="{{ route('dosen.mahasiswa.msib') }}" class="sidebar-link {{ request()->routeIs('dosen.mahasiswa.msib') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-rectangle w-5 text-center text-lg"></i>
                    <span>Mahasiswa MSIB</span>
                </a>

                <div class="pt-5 pb-2">
                    <p class="text-[10px] font-black tracking-widest text-blue-200/50 uppercase pl-3">Penilaian Evaluasi</p>
                </div>
                <a href="{{ route('dosen.nilai.pkl') }}" class="sidebar-link {{ request()->routeIs('dosen.nilai.pkl') ? 'active' : '' }}">
                    <i class="fa-solid fa-star-half-stroke w-5 text-center text-lg"></i>
                    <span>Nilai Akhir PKL</span>
                </a>
                <a href="{{ route('dosen.nilai.msib') }}" class="sidebar-link {{ request()->routeIs('dosen.nilai.msib') ? 'active' : '' }}">
                    <i class="fa-solid fa-star w-5 text-center text-lg"></i>
                    <span>Nilai Akhir MSIB</span>
                </a>
            @endif

            @if($role === 'mentor')
                <div class="pt-5 pb-2">
                    <p class="text-[10px] font-black tracking-widest text-blue-200/50 uppercase pl-3">Peserta Magang</p>
                </div>
                <a href="{{ route('mentor.mahasiswa.pkl') }}" class="sidebar-link {{ request()->routeIs('mentor.mahasiswa.pkl') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-viewfinder w-5 text-center text-lg"></i>
                    <span>Mahasiswa PKL</span>
                </a>
                <a href="{{ route('mentor.mahasiswa.msib') }}" class="sidebar-link {{ request()->routeIs('mentor.mahasiswa.msib') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-rectangle w-5 text-center text-lg"></i>
                    <span>Mahasiswa MSIB</span>
                </a>

                <div class="pt-5 pb-2">
                    <p class="text-[10px] font-black tracking-widest text-blue-200/50 uppercase pl-3">Beri Penilaian</p>
                </div>
                <a href="{{ route('mentor.nilai.pkl') }}" class="sidebar-link {{ request()->routeIs('mentor.nilai.pkl') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-check w-5 text-center text-lg"></i>
                    <span>Nilai Instansi PKL</span>
                </a>
                <a href="{{ route('mentor.nilai.msib') }}" class="sidebar-link {{ request()->routeIs('mentor.nilai.msib') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-list w-5 text-center text-lg"></i>
                    <span>Nilai Instansi MSIB</span>
                </a>
            @endif
        </nav>

        {{-- Sidebar Footer — Profile & Logout --}}
        <div class="shrink-0 p-4 border-t border-white/10 dark:border-surface-700/50 bg-black/10 dark:bg-black/20">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 group rounded-xl px-3 py-2 hover:bg-white/10 dark:hover:bg-white/5 transition-colors mb-2">
                <div class="w-9 h-9 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-inner">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate group-hover:text-amber-300 transition-colors">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-blue-200/70 truncate capitalize">{{ auth()->user()->role->value }} <span class="mx-1">•</span> Profil</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-red-200 bg-red-900/40 hover:bg-red-500 hover:text-white border border-red-500/30 transition-all duration-200">
                    <i class="fa-solid fa-power-off"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ============================================
         MAIN CONTENT
         ============================================ --}}
    <div class="lg:pl-64 min-h-screen flex flex-col">

        {{-- Top Bar --}}
        <header class="h-16 bg-white/80 dark:bg-surface-900/80 backdrop-blur-xl border-b border-gray-200/80 dark:border-surface-700/50 sticky top-0 z-30">
            <div class="h-full px-4 sm:px-6 flex items-center justify-between">
                {{-- Hamburger --}}
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-surface-800 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    {{-- Page Title --}}
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white hidden sm:block">
                        {{ $title ?? 'Dashboard' }}
                    </h2>
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-3">
                    {{-- Live Clock --}}
                    <div class="hidden sm:flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400" x-data="{ time: '{{ now()->setTimezone('Asia/Jakarta')->format('H.i.s') }}', date: '{{ now()->setTimezone('Asia/Jakarta')->format('l, d M Y') }}' }" x-init="
                        const serverTime = new Date('{{ now()->setTimezone('Asia/Jakarta')->toIso8601String() }}');
                        const clientTime = new Date();
                        const offset = serverTime.getTime() - clientTime.getTime();
                        const update = () => {
                            const now = new Date(Date.now() + offset);
                            const formatter = new Intl.DateTimeFormat('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Asia/Jakarta' });
                            const dateFormatter = new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric', timeZone: 'Asia/Jakarta' });
                            time = formatter.format(now);
                            date = dateFormatter.format(now);
                        };
                        update();
                        setInterval(update, 1000);
                    ">
                        <div class="text-right">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300" x-text="time"></p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500" x-text="date"></p>
                        </div>
                        <div class="w-px h-8 bg-gray-200 dark:bg-surface-700 mx-1"></div>
                    </div>

                    {{-- Dark Mode Toggle --}}
                    <button @click="darkMode = !darkMode"
                            class="p-2.5 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-surface-800 transition-all duration-200"
                            :title="darkMode ? 'Mode Terang' : 'Mode Gelap'">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>

                    {{-- User Info --}}
                    <div class="hidden sm:flex items-center gap-2.5 pl-2">
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-800 dark:text-white leading-tight">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">{{ auth()->user()->role->value }}</p>
                        </div>
                        <div class="w-9 h-9 bg-gradient-to-br from-primary-400 to-primary-600 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-md shadow-primary-500/20">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        <div class="px-4 sm:px-6 pt-4">
            @if(session('success'))
                <div class="alert-success animate-fade-in" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert-error animate-fade-in" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                     x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 p-4 sm:p-6">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="px-6 py-4 border-t border-gray-200/60 dark:border-surface-700/50 text-center text-xs text-gray-400 dark:text-gray-600">
            &copy; {{ date('Y') }} Sistem PKL — Universitas Bina Sarana Informatika
        </footer>
    </div>

    {{-- AI Chatbot Floating Button --}}
    @include('components.ai-chatbot')

</body>
</html>
