{{-- Detail Modal --}}
<div x-show="modalOpen" 
     x-cloak
     @keydown.escape.window="modalOpen = false"
     class="fixed inset-0 z-50 overflow-y-auto" 
     style="display: none;">
    
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
         @click="modalOpen = false"></div>
    
    {{-- Modal Panel --}}
    <div class="flex min-h-full items-center justify-center p-4">
        <div @click.stop 
             class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-3xl w-full max-h-[85vh] overflow-hidden flex flex-col">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-blue-600"></i>
                    Detail Data Mahasiswa
                </h3>
                <button @click="modalOpen = false" 
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            
            {{-- Body (scrollable) --}}
            <div class="flex-1 overflow-y-auto px-6 py-4">
                
                {{-- Loading State --}}
                <div x-show="modalLoading" class="flex flex-col items-center justify-center py-12">
                    <i class="fa-solid fa-spinner fa-spin text-4xl text-blue-600 mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Memuat data mahasiswa...</p>
                </div>
                
                {{-- Error State --}}
                <div x-show="!modalLoading && modalError" 
                     class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-exclamation-circle text-red-600 text-xl"></i>
                        <div>
                            <p class="font-medium text-red-800 dark:text-red-200">Gagal Memuat Data</p>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1" x-text="modalError"></p>
                        </div>
                    </div>
                </div>
                
                {{-- Content --}}
                <div x-show="!modalLoading && !modalError && modalData" class="space-y-5">
                    
                    {{-- Informasi Mahasiswa --}}
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-5 border border-blue-100 dark:border-blue-800">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-user text-blue-600"></i>
                            Informasi Mahasiswa
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Nama Lengkap</label>
                                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white" x-text="modalData?.user?.name"></p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">NIM</label>
                                <p class="mt-1 text-sm font-mono font-semibold text-blue-600 dark:text-blue-400" x-text="modalData?.user?.nim"></p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Email Personal</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white break-all" x-text="modalData?.user?.email || '-'"></p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Email BSI</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white break-all" x-text="modalData?.user?.email_bsi || '-'"></p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">No. HP</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="modalData?.user?.phone || '-'"></p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Kode Lokal</label>
                                <p class="mt-1 text-sm text-gray-900 dark:text-white" x-text="modalData?.user?.kd_lokal || '-'"></p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Dosen PA</label>
                                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white" x-text="modalData?.user?.nama_dosen_pa || '-'"></p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Data PKL (if exists) --}}
                    <div x-show="modalData?.proposal">
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-lg p-5 border border-emerald-100 dark:border-emerald-800">
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-briefcase text-emerald-600"></i>
                                Data PKL/MSIB
                            </h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Jenis PKL</label>
                                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white" x-text="modalData?.proposal?.jns_pkl"></p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tempat Riset/Perusahaan</label>
                                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white" x-text="modalData?.proposal?.tempat_riset"></p>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Judul/Kegiatan PKL</label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white leading-relaxed" x-text="modalData?.proposal?.judul_pkl"></p>
                                </div>
                                
                                {{-- Mentor Info --}}
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Pembimbing Lapangan (Mentor)</p>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label class="text-xs text-gray-500 dark:text-gray-400">Nama</label>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="modalData?.proposal?.nama_mentor"></p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 dark:text-gray-400">No. HP</label>
                                            <p class="text-sm text-gray-900 dark:text-white" x-text="modalData?.proposal?.hp_mentor"></p>
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 dark:text-gray-400">Email</label>
                                            <p class="text-sm text-gray-900 dark:text-white break-all" x-text="modalData?.proposal?.email_mentor"></p>
                                        </div>
                                    </div>
                                    <div class="mt-3" x-show="modalData?.proposal?.email_perusahaan">
                                        <label class="text-xs text-gray-500 dark:text-gray-400">Email Perusahaan</label>
                                        <p class="text-sm text-gray-900 dark:text-white break-all" x-text="modalData?.proposal?.email_perusahaan"></p>
                                    </div>
                                </div>
                                
                                {{-- Document Status --}}
                                <div>
                                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">Kelengkapan Dokumen</p>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                                        <div :class="modalData?.proposal?.files?.skm ? 'bg-emerald-100 dark:bg-emerald-900/30 border-emerald-300 dark:border-emerald-700' : 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600'" 
                                             class="rounded-lg p-3 border text-center">
                                            <i :class="modalData?.proposal?.files?.skm ? 'fa-check-circle text-emerald-600 dark:text-emerald-400' : 'fa-times-circle text-gray-400'" 
                                               class="fa-solid text-2xl mb-1"></i>
                                            <p class="text-xs font-medium" :class="modalData?.proposal?.files?.skm ? 'text-emerald-800 dark:text-emerald-200' : 'text-gray-500 dark:text-gray-400'">SKM</p>
                                        </div>
                                        <div :class="modalData?.proposal?.files?.proposal ? 'bg-emerald-100 dark:bg-emerald-900/30 border-emerald-300 dark:border-emerald-700' : 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600'" 
                                             class="rounded-lg p-3 border text-center">
                                            <i :class="modalData?.proposal?.files?.proposal ? 'fa-check-circle text-emerald-600 dark:text-emerald-400' : 'fa-times-circle text-gray-400'" 
                                               class="fa-solid text-2xl mb-1"></i>
                                            <p class="text-xs font-medium" :class="modalData?.proposal?.files?.proposal ? 'text-emerald-800 dark:text-emerald-200' : 'text-gray-500 dark:text-gray-400'">Proposal</p>
                                        </div>
                                        <div :class="modalData?.proposal?.files?.lp ? 'bg-emerald-100 dark:bg-emerald-900/30 border-emerald-300 dark:border-emerald-700' : 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600'" 
                                             class="rounded-lg p-3 border text-center">
                                            <i :class="modalData?.proposal?.files?.lp ? 'fa-check-circle text-emerald-600 dark:text-emerald-400' : 'fa-times-circle text-gray-400'" 
                                               class="fa-solid text-2xl mb-1"></i>
                                            <p class="text-xs font-medium" :class="modalData?.proposal?.files?.lp ? 'text-emerald-800 dark:text-emerald-200' : 'text-gray-500 dark:text-gray-400'">Laporan</p>
                                        </div>
                                        <div :class="modalData?.proposal?.files?.lpp ? 'bg-emerald-100 dark:bg-emerald-900/30 border-emerald-300 dark:border-emerald-700' : 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600'" 
                                             class="rounded-lg p-3 border text-center">
                                            <i :class="modalData?.proposal?.files?.lpp ? 'fa-check-circle text-emerald-600 dark:text-emerald-400' : 'fa-times-circle text-gray-400'" 
                                               class="fa-solid text-2xl mb-1"></i>
                                            <p class="text-xs font-medium" :class="modalData?.proposal?.files?.lpp ? 'text-emerald-800 dark:text-emerald-200' : 'text-gray-500 dark:text-gray-400'">LPP</p>
                                        </div>
                                        <div :class="modalData?.proposal?.files?.skp ? 'bg-emerald-100 dark:bg-emerald-900/30 border-emerald-300 dark:border-emerald-700' : 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600'" 
                                             class="rounded-lg p-3 border text-center">
                                            <i :class="modalData?.proposal?.files?.skp ? 'fa-check-circle text-emerald-600 dark:text-emerald-400' : 'fa-times-circle text-gray-400'" 
                                               class="fa-solid text-2xl mb-1"></i>
                                            <p class="text-xs font-medium" :class="modalData?.proposal?.files?.skp ? 'text-emerald-800 dark:text-emerald-200' : 'text-gray-500 dark:text-gray-400'">SKP</p>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Nilai (if exists) --}}
                                <div x-show="modalData?.proposal?.nilai && modalData.proposal.nilai > 0" 
                                     class="bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 rounded-lg p-4 border border-amber-200 dark:border-amber-800">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-medium text-amber-800 dark:text-amber-200 uppercase tracking-wide">Nilai Akhir PKL</p>
                                            <p class="text-4xl font-bold text-amber-600 dark:text-amber-400 mt-1 tabular-nums" x-text="modalData?.proposal?.nilai"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Dinilai oleh:</p>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white mt-1" x-text="modalData?.proposal?.penilai || '-'"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- No Proposal Warning --}}
                    <div x-show="!modalData?.proposal" 
                         class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-exclamation-triangle text-yellow-600 text-xl"></i>
                            <div>
                                <p class="font-medium text-yellow-800 dark:text-yellow-200">Belum Ada Data PKL</p>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">Mahasiswa ini belum mengisi data PKL/MSIB.</p>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <button @click="modalOpen = false"
                        class="w-full sm:w-auto px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md text-sm font-medium transition-colors">
                    <i class="fa-solid fa-times mr-2"></i>
                    Tutup
                </button>
            </div>
            
        </div>
    </div>
</div>
