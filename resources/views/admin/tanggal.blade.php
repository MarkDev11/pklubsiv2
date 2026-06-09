<x-app-layout>
    <x-slot name="title">Pengaturan Tanggal & Timeline</x-slot>

    <div class="space-y-6 max-w-5xl mx-auto">
        
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Timeline Master</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Atur buka-tutup akses fitur khusus mahasiswa dan dosen secara otomatis sesuai tenggat waktu yang Anda tentukan.
            </p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.tanggal.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Card 1: Input Data PKL --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-md bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">1. Input Identitas PKL</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Fase Pendaftaran & Proposal</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        {{-- Open Time --}}
                        <div>
                            <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Buka Akses
                            </label>
                            <input type="datetime-local" name="open_time" value="{{ $openingHours?->open_time?->format('Y-m-d\TH:i') }}" 
                                   class="w-full px-3 py-2 text-sm font-mono bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        
                        {{-- Close Time --}}
                        <div>
                            <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Tutup Akses
                            </label>
                            <input type="datetime-local" name="close_time" value="{{ $openingHours?->close_time?->format('Y-m-d\TH:i') }}" 
                                   class="w-full px-3 py-2 text-sm font-mono bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            @error('close_time') <span class="text-xs text-red-600 dark:text-red-400 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Card 2: Upload Laporan --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-md bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">2. Unggah Dokumen</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Fase Laporan, LPP, SKP</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Buka Akses
                            </label>
                            <input type="datetime-local" name="open_laporan" value="{{ $openingHours?->open_laporan?->format('Y-m-d\TH:i') }}" 
                                   class="w-full px-3 py-2 text-sm font-mono bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" required>
                        </div>
                        
                        <div>
                            <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Tutup Akses
                            </label>
                            <input type="datetime-local" name="close_laporan" value="{{ $openingHours?->close_laporan?->format('Y-m-d\TH:i') }}" 
                                   class="w-full px-3 py-2 text-sm font-mono bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" required>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Input Nilai --}}
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-md bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <i class="fa-solid fa-star-half-stroke"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white">3. Penilaian Dosen</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Fase Sidang & Nilai Akhir</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Buka Akses
                            </label>
                            <input type="datetime-local" name="open_nilai" value="{{ $openingHours?->open_nilai?->format('Y-m-d\TH:i') }}" 
                                   class="w-full px-3 py-2 text-sm font-mono bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                        </div>
                        
                        <div>
                            <label class="flex items-center gap-2 text-xs font-medium text-gray-700 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span> Tutup Akses
                            </label>
                            <input type="datetime-local" name="close_nilai" value="{{ $openingHours?->close_nilai?->format('Y-m-d\TH:i') }}" 
                                   class="w-full px-3 py-2 text-sm font-mono bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="window.location.reload()" 
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Reset Form
                </button>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                    <i class="fa-solid fa-save"></i>
                    <span>Terapkan Timeline</span>
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
