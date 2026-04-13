<x-app-layout>
    <x-slot name="title">Nilai PKL Magang</x-slot>
    <div class="space-y-6 animate-fade-in">
        <div class="card !p-0 overflow-hidden border border-gray-200 dark:border-surface-700">
            
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-8 relative">
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center text-white text-2xl shadow-lg border border-white/10">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white tracking-tight">Nilai PKL — Magang Reguler</h3>
                            <p class="text-blue-100 text-sm mt-0.5 flex items-center gap-2">
                                <i class="fa-solid fa-users text-xs"></i> {{ $proposals->count() }} mahasiswa terdaftar
                            </p>
                        </div>
                    </div>
                </div>
                {{-- Decorative circles --}}
                <div class="absolute -right-8 -top-8 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute right-20 -bottom-8 w-28 h-28 bg-blue-400/20 rounded-full blur-xl"></div>
            </div>

            <div class="p-6">
                @if($proposals->count())
                <form method="POST" action="{{ route(str_contains(request()->route()->getName(), 'admin') ? 'admin.nilai.save' : (str_contains(request()->route()->getName(), 'dosen') ? 'dosen.nilai.save' : 'mentor.nilai.save')) }}">
                    @csrf
                    <div class="table-container rounded-xl border border-gray-200 dark:border-surface-700">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-surface-800 border-b border-gray-200 dark:border-surface-700 text-gray-500 dark:text-gray-400">
                                    <th class="text-center w-12 py-4">No</th>
                                    <th>Mahasiswa</th>
                                    <th>Tempat & Mentor</th>
                                    <th class="text-center">Kelengkapan</th>
                                    <th class="text-center w-32">Input Nilai</th>
                                    <th>Informasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-surface-700">
                                @foreach($proposals as $i => $p)
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-surface-800/80 transition-colors">
                                    <td class="text-center font-medium text-gray-400">{{ $i + 1 }}</td>
                                    
                                    <td>
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $p->nama }}</div>
                                        <div class="font-mono text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $p->nim }}</div>
                                    </td>
                                    
                                    <td>
                                        <div class="flex items-start gap-2">
                                            <i class="fa-solid fa-building text-gray-400 mt-1"></i>
                                            <div>
                                                <div class="font-medium text-gray-800 dark:text-gray-200">{{ $p->tempat_riset }}</div>
                                                <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5"><i class="fa-solid fa-user-tie text-[10px]"></i> {{ $p->nama_mentor ?? 'Belum ada mentor' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="flex justify-center gap-1.5">
                                            <span class="px-2 py-1 rounded text-[10px] font-bold border {{ $p->lp ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800' : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800' }}" title="Laporan PKL">LP</span>
                                            <span class="px-2 py-1 rounded text-[10px] font-bold border {{ $p->lpp ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800' : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800' }}" title="Lembar Penilaian Perusahaan">LPP</span>
                                            <span class="px-2 py-1 rounded text-[10px] font-bold border {{ $p->skp ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800' : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800' }}" title="Sertifikat / SKK">SKP</span>
                                        </div>
                                    </td>
                                    
                                    <td class="text-center">
                                        <input type="hidden" name="form_id[]" value="{{ $p->id }}">
                                        <div class="relative inline-block w-20">
                                            <input type="number" name="nilai[]" value="{{ $p->nilai }}" min="0" max="100"
                                                class="form-input w-full text-center py-2 pr-2 font-bold text-lg focus:ring-blue-500 focus:border-blue-500 {{ $p->nilai > 0 ? 'bg-blue-50 border-blue-300 dark:bg-blue-900/20 dark:border-blue-700 text-blue-700 dark:text-blue-400' : 'bg-white dark:bg-surface-800 border-gray-300 dark:border-surface-600' }}">
                                        </div>
                                    </td>
                                    
                                    <td>
                                        <div class="text-xs space-y-1">
                                            <div class="flex items-center gap-1 text-gray-500 dark:text-gray-400"><i class="fa-solid fa-user-pen w-3"></i> <span>{{ $p->penilai ?? 'Belum dinilai' }}</span></div>
                                            <div class="flex items-center gap-1 text-gray-400 dark:text-gray-500"><i class="fa-solid fa-clock w-3"></i> <span>{{ $p->updated_at?->format('d M Y, H:i') }}</span></div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 border-t border-gray-100 dark:border-surface-700 pt-6">
                        <div class="w-full sm:w-auto">
                            {{ $proposals->links() }}
                        </div>
                        <button type="submit" class="btn-primary hover:-translate-y-1 transition-transform shadow-lg shadow-blue-500/30 px-8 py-2.5 rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Rekap Nilai
                        </button>
                    </div>
                </form>
                @else
                <div class="text-center py-16 px-4">
                    <div class="w-20 h-20 bg-gray-50 dark:bg-surface-800 border border-gray-100 dark:border-surface-700 rounded-full flex items-center justify-center text-3xl mx-auto mb-5 shadow-sm text-gray-400">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Data Kosong</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">Saat ini belum ada mahasiswa jalur PKL Magang Reguler yang dapat dinilai.</p>
                </div>
                @endif
            </div>
            
        </div>
    </div>
</x-app-layout>
