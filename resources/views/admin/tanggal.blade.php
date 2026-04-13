<x-app-layout>
    <x-slot name="title">Pengaturan Tanggal & Sinkronisasi</x-slot>

    <div class="space-y-6 max-w-5xl mx-auto animate-fade-in text-gray-800 dark:text-gray-200">
        
        {{-- Hero Banner --}}
        <div class="rounded-2xl border border-gray-200 dark:border-surface-700 bg-white dark:bg-surface-800 overflow-hidden shadow-sm relative">
            <div class="absolute inset-0 bg-gradient-to-r from-primary-600/10 to-indigo-600/10 dark:from-primary-900/20 dark:to-indigo-900/20"></div>
            <div class="p-6 sm:p-8 relative z-10 flex flex-col sm:flex-row sm:items-center gap-5 justify-between">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 bg-white dark:bg-surface-800 shadow-md border border-gray-100 dark:border-surface-700 rounded-2xl flex items-center justify-center text-primary-600 text-2xl flex-shrink-0">
                        <i class="fa-solid fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Timeline Master</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Atur buka-tutup akses fitur khusus mahasiswa dan dosen secara otomatis sesuai tenggat waktu yang Anda tentukan.</p>
                    </div>
                </div>
                <div class="hidden sm:block text-right">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-xs font-bold border border-green-200 dark:border-green-800/30">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        Kronologi Aktif
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Start --}}
        <form method="POST" action="{{ route('admin.tanggal.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Card 1: Input Data PKL --}}
                <div class="group relative bg-white dark:bg-surface-800 rounded-2xl p-6 border border-gray-200 dark:border-surface-700 shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-x-0 -top-px h-1 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-t-2xl opacity-70 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">1. Input Identitas PKL</h3>
                            <p class="text-[11px] text-gray-500">Fase Pendaftaran & Proposal</p>
                        </div>
                    </div>

                    <div class="space-y-4 relative">
                        <div class="absolute left-3.5 top-8 bottom-8 w-0.5 bg-dashed border-l-2 border-dashed border-gray-200 dark:border-surface-700 -z-10"></div>
                        
                        {{-- Open Time --}}
                        <div class="relative bg-gray-50/50 dark:bg-surface-900/50 p-4 rounded-xl border border-gray-100 dark:border-surface-700">
                            <label class="flex items-center gap-2 text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span> Buka Akses
                            </label>
                            <input type="datetime-local" name="open_time" value="{{ $openingHours?->open_time?->format('Y-m-d\TH:i') }}" class="form-input w-full text-sm font-mono dark:bg-surface-800" required>
                        </div>
                        
                        {{-- Close Time --}}
                        <div class="relative bg-gray-50/50 dark:bg-surface-900/50 p-4 rounded-xl border border-gray-100 dark:border-surface-700">
                            <label class="flex items-center gap-2 text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Tutup Akses
                            </label>
                            <input type="datetime-local" name="close_time" value="{{ $openingHours?->close_time?->format('Y-m-d\TH:i') }}" class="form-input w-full text-sm font-mono dark:bg-surface-800 border-red-200 focus:border-red-500 focus:ring-red-500/20" required>
                        </div>
                        @error('close_time') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Card 2: Upload Laporan --}}
                <div class="group relative bg-white dark:bg-surface-800 rounded-2xl p-6 border border-gray-200 dark:border-surface-700 shadow-sm hover:shadow-lg transition-all duration-300 transform lg:-translate-y-2">
                    <div class="absolute inset-x-0 -top-px h-1 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-t-2xl opacity-70 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">2. Unggah Dokumen</h3>
                            <p class="text-[11px] text-gray-500">Fase Laporan, LPP, SKP</p>
                        </div>
                    </div>

                    <div class="space-y-4 relative">
                        <div class="absolute left-3.5 top-8 bottom-8 w-0.5 bg-dashed border-l-2 border-dashed border-gray-200 dark:border-surface-700 -z-10"></div>
                        
                        <div class="relative bg-gray-50/50 dark:bg-surface-900/50 p-4 rounded-xl border border-gray-100 dark:border-surface-700">
                            <label class="flex items-center gap-2 text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span> Buka Akses
                            </label>
                            <input type="datetime-local" name="open_laporan" value="{{ $openingHours?->open_laporan?->format('Y-m-d\TH:i') }}" class="form-input w-full text-sm font-mono dark:bg-surface-800" required>
                        </div>
                        
                        <div class="relative bg-gray-50/50 dark:bg-surface-900/50 p-4 rounded-xl border border-gray-100 dark:border-surface-700 relative text-red-500">
                            <label class="flex items-center gap-2 text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Tutup Akses
                            </label>
                            <input type="datetime-local" name="close_laporan" value="{{ $openingHours?->close_laporan?->format('Y-m-d\TH:i') }}" class="form-input w-full text-sm font-mono dark:bg-surface-800 border-red-200 focus:border-red-500 focus:ring-red-500/20" required>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Input Nilai --}}
                <div class="group relative bg-white dark:bg-surface-800 rounded-2xl p-6 border border-gray-200 dark:border-surface-700 shadow-sm hover:shadow-lg transition-all duration-300">
                    <div class="absolute inset-x-0 -top-px h-1 bg-gradient-to-r from-amber-400 to-orange-500 rounded-t-2xl opacity-70 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <i class="fa-solid fa-star-half-stroke"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">3. Penilaian Dosen</h3>
                            <p class="text-[11px] text-gray-500">Fase Sidang & Nilai Akhir</p>
                        </div>
                    </div>

                    <div class="space-y-4 relative">
                        <div class="absolute left-3.5 top-8 bottom-8 w-0.5 bg-dashed border-l-2 border-dashed border-gray-200 dark:border-surface-700 -z-10"></div>
                        
                        <div class="relative bg-gray-50/50 dark:bg-surface-900/50 p-4 rounded-xl border border-gray-100 dark:border-surface-700">
                            <label class="flex items-center gap-2 text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span> Buka Akses
                            </label>
                            <input type="datetime-local" name="open_nilai" value="{{ $openingHours?->open_nilai?->format('Y-m-d\TH:i') }}" class="form-input w-full text-sm font-mono dark:bg-surface-800" required>
                        </div>
                        
                        <div class="relative bg-gray-50/50 dark:bg-surface-900/50 p-4 rounded-xl border border-gray-100 dark:border-surface-700 relative text-red-500">
                            <label class="flex items-center gap-2 text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Tutup Akses
                            </label>
                            <input type="datetime-local" name="close_nilai" value="{{ $openingHours?->close_nilai?->format('Y-m-d\TH:i') }}" class="form-input w-full text-sm font-mono dark:bg-surface-800 border-red-200 focus:border-red-500 focus:ring-red-500/20" required>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-surface-700">
                <button type="button" onclick="window.location.reload()" class="btn px-6 text-gray-600 bg-gray-100 dark:bg-surface-700 hover:bg-gray-200 dark:hover:bg-surface-600 font-semibold border-transparent">
                    Reset Form
                </button>
                <button type="submit" class="btn-primary px-8 shadow-lg shadow-primary-500/20 flex items-center gap-2 transform transition-transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-save"></i> Terapkan Timeline
                </button>
            </div>
            
        </form>
    </div>
</x-app-layout>
