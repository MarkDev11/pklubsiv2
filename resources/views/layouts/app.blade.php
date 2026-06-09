<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ 
          darkMode: localStorage.getItem('darkMode') === 'true', 
          sidebarOpen: false, 
          sidebarExpanded: localStorage.getItem('sidebar-expanded') === 'true'
      }"
      x-init="
          $watch('darkMode', val => localStorage.setItem('darkMode', val));
          $watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value));
      "
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — PKL UBSI</title>
    <link rel="icon" type="image/png" href="{{ asset('images/pkl_logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script>
        if (localStorage.getItem('darkMode') === 'true') {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
        }
        if (localStorage.getItem('sidebar-expanded') === 'true') {
            document.body.classList.add('sidebar-expanded');
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white h-screen overflow-hidden">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-cloak x-transition.opacity
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         @click="sidebarOpen = false"></div>

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside 
            class="fixed lg:static inset-y-0 left-0 z-50 flex flex-col transition-all duration-300 ease-in-out"
            :class="{
                'translate-x-0': sidebarOpen,
                '-translate-x-full lg:translate-x-0': !sidebarOpen,
                'w-72': sidebarExpanded || window.innerWidth < 1024,
                'w-20': !sidebarExpanded && window.innerWidth >= 1024
            }"
        >
            <div class="flex flex-col h-full border-r border-blue-300 dark:border-gray-700 overflow-hidden bg-gradient-to-br from-blue-200 via-indigo-200 to-blue-300 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800">
                
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between h-16 px-4 border-b border-blue-300 dark:border-gray-700 bg-gradient-to-r from-blue-200/80 to-indigo-200/80 dark:from-gray-800/80 dark:to-gray-800/80">
                    <a href="{{ route(auth()->user()->role->value . '.dashboard') }}" 
                       class="flex items-center overflow-hidden"
                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center w-full'">
                        <img src="{{ asset('images/logo1.png') }}" alt="UBSI" class="h-8 w-8 object-contain flex-shrink-0">
                        <div class="flex flex-col" 
                             x-show="sidebarExpanded || window.innerWidth < 1024"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0">
                            <span class="text-sm font-bold text-gray-800 dark:text-white whitespace-nowrap">PKL UBSI</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Management System</span>
                        </div>
                    </a>
                    
                    <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 px-3 py-4 overflow-y-auto custom-scrollbar">
                    @php $role = auth()->user()->role->value; @endphp
                    
                    <!-- Dashboard -->
                    <div class="mb-6">
                        <div class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider transition-opacity"
                             :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                            Utama
                        </div>
                        
                        <ul class="space-y-1">
                            <li>
                                <a href="{{ route($role . '.dashboard') }}"
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs($role . '.dashboard') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                          :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                        Dashboard
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Role Menu -->
                    <div class="mb-6">
                        <div class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider transition-opacity"
                             :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                            Menu
                        </div>
                        
                        <ul class="space-y-1">
                            @if($role === 'mahasiswa')
                                <li>
                                    <a href="{{ route('mahasiswa.proposal.index') }}"
                                       data-ai-target="sidebar.mahasiswa.proposal"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('mahasiswa.proposal.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Data PKL
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('mahasiswa.laporan.index') }}"
                                       data-ai-target="sidebar.mahasiswa.laporan"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('mahasiswa.laporan.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Upload Laporan
                                        </span>
                                    </a>
                                </li>
                                
                            @elseif($role === 'dosen')
                                <li>
                                    <a href="{{ route('dosen.mahasiswa.pkl') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('dosen.mahasiswa.pkl') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Mahasiswa PKL
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('dosen.nilai.pkl') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('dosen.nilai.pkl') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Nilai PKL
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('dosen.mahasiswa.msib') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('dosen.mahasiswa.msib') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Mahasiswa MSIB
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('dosen.nilai.msib') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('dosen.nilai.msib') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Nilai MSIB
                                        </span>
                                    </a>
                                </li>

                                {{-- Section: Export Berkas --}}
                                <li class="pt-5 pb-1" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                    <div class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Export Berkas
                                    </div>
                                </li>

                                <li>
                                    <a href="{{ route('dosen.nilai.pkl') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('dosen.nilai.pkl') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Export PKL
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('dosen.nilai.msib') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('dosen.nilai.msib') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Export MSIB
                                        </span>
                                    </a>
                                </li>

                            @elseif($role === 'mentor')
                                <li>
                                    <a href="{{ route('mentor.mahasiswa.pkl') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('mentor.mahasiswa.pkl') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Mahasiswa PKL
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('mentor.nilai.pkl') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('mentor.nilai.pkl') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Penilaian
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('mentor.mahasiswa.msib') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('mentor.mahasiswa.msib') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Mahasiswa MSIB
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('mentor.nilai.msib') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('mentor.nilai.msib') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Penilaian MSIB
                                        </span>
                                    </a>
                                </li>

                                {{-- Section: Export Berkas --}}
                                <li class="pt-5 pb-1" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                    <div class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Export Berkas
                                    </div>
                                </li>

                                <li>
                                    <a href="{{ route('mentor.nilai.pkl') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('mentor.nilai.pkl') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Export PKL
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('mentor.nilai.msib') }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('mentor.nilai.msib') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap transition-opacity duration-300"
                                              :class="(sidebarExpanded || window.innerWidth < 1024) ? 'opacity-100' : 'opacity-0 lg:opacity-0'">
                                            Export MSIB
                                        </span>
                                    </a>
                                </li>

                            @elseif($role === 'admin')
                                {{-- Section: Kelola Sistem --}}
                                <li class="pt-3 pb-1" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                    <div class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Kelola Sistem
                                    </div>
                                </li>
                                
                                <li>
                                    <a href="{{ route('admin.akun.index') }}"
                                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.akun.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}"
                                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center'">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                            Daftar Akun
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.import.index') }}"
                                       data-ai-target="sidebar.admin.import"
                                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.import.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}"
                                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center'">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                            Import Data
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.mahasiswa.index') }}"
                                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.mahasiswa.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}"
                                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center'">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                            Data Mahasiswa
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.tanggal.index') }}"
                                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.tanggal.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}"
                                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center'">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                            Pengaturan Tanggal
                                        </span>
                                    </a>
                                </li>
                                
                                {{-- Section: Penilaian Akhir --}}
                                <li class="pt-5 pb-1" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                    <div class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Penilaian Akhir
                                    </div>
                                </li>
                                
                                <li>
                                    <a href="{{ route('admin.nilai.pkl') }}"
                                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.nilai.pkl') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}"
                                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center'">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                            Nilai PKL Magang
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.nilai.msib') }}"
                                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.nilai.msib') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}"
                                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center'">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                            Nilai MSIB / PMK
                                        </span>
                                    </a>
                                </li>
                                
                                {{-- Section: Export Data --}}
                                <li class="pt-5 pb-1" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                    <div class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                        Export Data
                                    </div>
                                </li>
                                
                                <li>
                                    <a href="{{ route('admin.exports.pkl.config') }}"
                                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.exports.pkl.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}"
                                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center'">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                            Export PKL
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.exports.msib.config') }}"
                                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.exports.msib.*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}"
                                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center'">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                            Export MSIB
                                        </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.exports.history') }}"
                                       class="flex items-center px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.exports.history') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md shadow-blue-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-white/60 dark:hover:bg-gray-700/60 hover:shadow-sm' }}"
                                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center'">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-3-6.708M21 3v5h-5"/>
                                        </svg>
                                        <span class="font-medium whitespace-nowrap" x-show="sidebarExpanded || window.innerWidth < 1024" x-transition>
                                            Export History
                                        </span>
                                    </a>
                                </li>
                                 
                            @endif
                        </ul>
                    </div>
                </nav>

                <!-- Sidebar Footer: Profile & Logout -->
                <div class="p-3 border-t border-blue-300 dark:border-gray-700 bg-gradient-to-r from-blue-200/80 to-indigo-200/80 dark:from-gray-800/80 dark:to-gray-800/80">
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center group rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors mb-2"
                       :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-3' : 'justify-center'">
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0"
                             x-show="sidebarExpanded || window.innerWidth < 1024"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0">
                            <p class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate capitalize">{{ auth()->user()->role->value }}</p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center rounded-lg py-2 text-xs font-bold uppercase tracking-wide text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 border border-red-200 dark:border-red-800 transition-all duration-200"
                                :class="(sidebarExpanded || window.innerWidth < 1024) ? 'gap-2 px-4' : 'px-2'">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024"
                                  x-transition:enter="transition ease-out duration-200"
                                  x-transition:enter-start="opacity-0"
                                  x-transition:enter-end="opacity-100"
                                  x-transition:leave="transition ease-in duration-150"
                                  x-transition:leave-start="opacity-100"
                                  x-transition:leave-end="opacity-0">
                                Sign Out
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Navbar -->
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-30">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        
                        <!-- Left: Toggle & Title -->
                        <div class="flex items-center gap-4">
                            <button @click="sidebarOpen = !sidebarOpen"
                                    class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            
                            <button @click="sidebarExpanded = !sidebarExpanded" 
                                    class="hidden lg:flex p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                </svg>
                            </button>

                            <!-- Page Title -->
                            <div class="flex items-center">
                                <span class="text-lg font-semibold text-gray-800 dark:text-white">{{ $title ?? 'Dashboard' }}</span>
                            </div>
                        </div>

                        <!-- Right: User Actions -->
                        <div class="flex items-center gap-3">
                            
                            <!-- Dark Mode -->
                            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode);"
                                    class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </button>

                            <!-- Notification Bell -->
                            @php
                                $notifications = [];
                                $role = auth()->user()->role->value;
                                
                                if ($role === 'admin') {
                                    $incomplete = \App\Models\ProposalMahasiswa::whereNull('lp')->orWhereNull('lpp')->orWhereNull('skp')->count();
                                    $ungraded = \App\Models\ProposalMahasiswa::where(function($q) { $q->whereNull('nilai')->orWhere('nilai', '<=', 0); })->count();
                                    if ($incomplete > 0) $notifications[] = ['icon' => 'file-circle-xmark', 'text' => "$incomplete mahasiswa dokumen belum lengkap", 'color' => 'amber'];
                                    if ($ungraded > 0) $notifications[] = ['icon' => 'clipboard-question', 'text' => "$ungraded mahasiswa belum dinilai", 'color' => 'blue'];
                                } elseif ($role === 'dosen') {
                                    $myStudents = \App\Models\ProposalMahasiswa::whereHas('user', fn($q) => $q->where('nama_dosen_pa', auth()->user()->username));
                                    $ungraded = $myStudents->clone()->where(function($q) { $q->whereNull('nilai')->orWhere('nilai', '<=', 0); })->count();
                                    if ($ungraded > 0) $notifications[] = ['icon' => 'clipboard-question', 'text' => "$ungraded mahasiswa bimbingan belum dinilai", 'color' => 'blue'];
                                } elseif ($role === 'mentor') {
                                    $myStudents = \App\Models\ProposalMahasiswa::where('email_mentor', auth()->user()->username);
                                    $ungraded = $myStudents->clone()->where(function($q) { $q->whereNull('nilai')->orWhere('nilai', '<=', 0); })->count();
                                    if ($ungraded > 0) $notifications[] = ['icon' => 'clipboard-question', 'text' => "$ungraded mahasiswa belum Anda nilai", 'color' => 'blue'];
                                } elseif ($role === 'mahasiswa') {
                                    $proposal = \App\Models\ProposalMahasiswa::where('user_id', auth()->id())->first();
                                    if ($proposal) {
                                        $incomplete = !$proposal->lp || !$proposal->lpp || !$proposal->skp;
                                        if ($incomplete) $notifications[] = ['icon' => 'file-circle-xmark', 'text' => 'Dokumen PKL Anda belum lengkap', 'color' => 'amber'];
                                        if ($proposal->nilai > 0) $notifications[] = ['icon' => 'circle-check', 'text' => "Nilai PKL Anda: {$proposal->nilai}", 'color' => 'emerald'];
                                    }
                                }
                                
                                // Deadline reminder (for all roles)
                                $openingHour = \App\Models\OpeningHour::first();
                                if ($openingHour && $openingHour->close_nilai) {
                                    $daysLeft = now()->diffInDays($openingHour->close_nilai, false);
                                    if ($daysLeft >= 0 && $daysLeft <= 7) {
                                        $notifications[] = ['icon' => 'clock', 'text' => "Deadline penilaian: " . ($daysLeft == 0 ? 'Hari ini!' : "$daysLeft hari lagi"), 'color' => 'red'];
                                    }
                                }
                                
                                $notifCount = count($notifications);
                            @endphp
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open" 
                                        class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                    @if($notifCount > 0)
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                    @endif
                                </button>
                                
                                <!-- Notification Dropdown -->
                                <div x-show="open" x-cloak
                                     x-transition
                                     class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                                    <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                        <span class="font-semibold text-gray-800 dark:text-white">Notifikasi</span>
                                        @if($notifCount > 0)
                                        <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded-full font-medium">{{ $notifCount }}</span>
                                        @endif
                                    </div>
                                    
                                    @if($notifCount > 0)
                                        <div class="max-h-96 overflow-y-auto">
                                            @foreach($notifications as $notif)
                                            <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-100 dark:border-gray-700/50 last:border-0">
                                                <div class="flex items-start gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-{{ $notif['color'] }}-100 dark:bg-{{ $notif['color'] }}-900/30 text-{{ $notif['color'] }}-600 dark:text-{{ $notif['color'] }}-400 flex items-center justify-center flex-shrink-0">
                                                        <i class="fa-solid fa-{{ $notif['icon'] }} text-sm"></i>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $notif['text'] }}</p>
                                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Baru saja</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="px-4 py-8 text-center">
                                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-2">
                                                <i class="fa-solid fa-bell-slash text-gray-400 text-lg"></i>
                                            </div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- User Profile -->
                            <div class="relative ml-2" x-data="{ open: false }" @click.away="open = false">
                                
                                <button @click="open = !open" 
                                        class="flex items-center gap-3 p-1.5 pr-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-sm">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </div>
                                    
                                    <div class="hidden md:block text-left">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate max-w-[120px]">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($role) }}</p>
                                    </div>
                                    
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <!-- User Dropdown -->
                                <div x-show="open" x-cloak x-transition
                                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                                    
                                    <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    </div>
                                    
                                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Profile
                                    </a>
                                    
                                    <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-4 sm:p-6 lg:p-8">
                
                <!-- Flash Messages -->
                @if(session('success'))
                    
                    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                        
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    
                    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                        
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('warning'))
                    
                    <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" x-transition>
                        
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span class="font-medium">{{ session('warning') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- AI Chatbot (authenticated users only) --}}
    @auth
        @include('components.ai-chatbot')
    @endauth
</body>
</html>
