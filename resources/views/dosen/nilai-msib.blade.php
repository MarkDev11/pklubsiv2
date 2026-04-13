<x-app-layout>
    <x-slot name="title">Konversi Nilai MSIB</x-slot>

    <div class="space-y-6 animate-fade-in pb-10">
        
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-100 dark:bg-cyan-500/10 text-cyan-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-medal text-xl"></i>
                    </div>
                    Konversi Nilai MSIB Nasional
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Konversikan mutu sertifikat kelulusan MSIB ke dalam komponen indeks prestasi mahasiswa.</p>
            </div>
            
            @if(isset($openingHours) && $openingHours->isNilaiBuka())
            <div class="bg-emerald-50 dark:bg-emerald-900/20 px-4 py-2 rounded-xl text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-100 dark:border-emerald-800 flex items-center gap-2">
                <i class="fa-solid fa-unlock text-sm"></i> Sesi Penilaian Terbuka
            </div>
            @endif
        </div>

        {{-- Verification Area --}}
        <div class="bg-white dark:bg-surface-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-surface-700 overflow-hidden relative p-1">
            
            @if(isset($openingHours) && !$openingHours->isNilaiBuka())
                <div class="m-5 rounded-2xl bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 p-8 text-center max-w-2xl mx-auto shadow-sm">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-800/20 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-200 dark:border-red-700/30">
                        <i class="fa-solid fa-lock text-3xl"></i>
                    </div>
                    <h3 class="font-black text-gray-900 dark:text-white text-xl mb-2">Portal Penilaian Terkunci</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Anda tidak dapat menyelaraskan/konversi nilai mahasiswa di luar jadwal kalender akademik yang telah ditetapkan oleh Administrator.</p>
                </div>
            @elseif($proposals->count())
                <form method="POST" action="{{ route('dosen.nilai.save') }}">
                    @csrf
                    <div class="overflow-x-auto custom-scrollbar rounded-[1.5rem] mt-2 border-t border-gray-100 dark:border-surface-700">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-surface-900 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest font-bold">
                                <tr>
                                    <th class="px-6 py-4 w-12 text-center rounded-tl-xl">No</th>
                                    <th class="px-6 py-4">Mahasiswa / Mitra</th>
                                    <th class="px-6 py-4 text-center">Kelengkapan Laporan</th>
                                    <th class="px-6 py-4 text-center">Terkahir Di-Update</th>
                                    <th class="px-6 py-4 text-right rounded-tr-xl">Mutu Konversi (0-100)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-surface-700 bg-white dark:bg-surface-800">
                                @foreach($proposals as $i => $p)
                                <tr class="hover:bg-cyan-50/30 dark:hover:bg-cyan-900/10 transition-colors group">
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-gray-500 dark:text-gray-400 font-bold">{{ $i+1 }}</span>
                                    </td>
                                    
                                    <td class="px-6 py-5">
                                        <div>
                                            <p class="font-bold text-gray-900 dark:text-white text-base">{{ $p->nama }}</p>
                                            <div class="flex items-center gap-3 mt-1">
                                                <span class="text-xs font-mono text-gray-500 bg-gray-100 dark:bg-surface-700 px-2 py-0.5 rounded">{{ $p->nim }}</span>
                                                <span class="text-xs text-cyan-600 dark:text-cyan-400 truncate max-w-[150px]"><i class="fa-solid fa-building mr-1"></i> {{ $p->tempat_riset }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- LP --}}
                                            <div class="group/tt relative flex flex-col items-center">
                                                <span class="w-8 h-8 rounded-lg flex items-center justify-center {{ $p->lp ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : 'bg-gray-100 dark:bg-surface-700 text-gray-400' }} border {{ $p->lp ? 'border-emerald-200 dark:border-emerald-800/50' : 'border-gray-200 dark:border-surface-600' }}">LP</span>
                                            </div>
                                            {{-- LPP --}}
                                            <div class="group/tt relative flex flex-col items-center">
                                                <span class="w-8 h-8 rounded-lg flex items-center justify-center {{ $p->lpp ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : 'bg-gray-100 dark:bg-surface-700 text-gray-400' }} border {{ $p->lpp ? 'border-emerald-200 dark:border-emerald-800/50' : 'border-gray-200 dark:border-surface-600' }}">LPP</span>
                                            </div>
                                            {{-- SKP --}}
                                            <div class="group/tt relative flex flex-col items-center">
                                                <span class="w-8 h-8 rounded-lg flex items-center justify-center {{ $p->skp ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600' : 'bg-gray-100 dark:bg-surface-700 text-gray-400' }} border {{ $p->skp ? 'border-emerald-200 dark:border-emerald-800/50' : 'border-gray-200 dark:border-surface-600' }}">SKP</span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-5 text-center">
                                        @if($p->updated_at)
                                            <p class="text-xs text-gray-500 font-medium">{{ $p->updated_at->format('d/m/Y') }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $p->penilai ?? '-' }}</p>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum Dikonversi</span>
                                        @endif
                                    </td>
                                    
                                    <td class="px-6 py-5 text-right w-48" x-data="{ 
                                            id: '{{ $p->id }}', 
                                            nilai: {{ $p->nilai ?: 'null' }}, 
                                            status: '{{ $p->nilai > 0 ? 'saved' : 'idle' }}',
                                            save() {
                                                if(this.nilai === null || this.nilai === '') return;
                                                this.status = 'saving';
                                                
                                                fetch('{{ route('dosen.nilai.save') }}', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json'
                                                    },
                                                    body: JSON.stringify({ form_id: [this.id], nilai: [this.nilai] })
                                                })
                                                .then(res => res.json())
                                                .then(data => {
                                                    this.status = 'saved';
                                                })
                                                .catch(err => {
                                                    this.status = 'error';
                                                    console.error(err);
                                                });
                                            }
                                        }">
                                        <input type="hidden" name="form_id[]" value="{{ $p->id }}">
                                        <div class="relative w-24 ml-auto">
                                            <input type="number" name="nilai[]" x-model="nilai" min="0" max="100" @input.debounce.5000ms="save"
                                                :class="{'border-emerald-300 dark:border-emerald-700 border-2': status === 'saved', 'border-gray-300 dark:border-surface-600 focus:border-cyan-500 focus:ring-cyan-500': status !== 'saved'}"
                                                class="w-full bg-gray-50 dark:bg-surface-900 border appearance-none text-gray-900 dark:text-white text-lg font-black text-center rounded-xl px-3 py-2 transition-colors">
                                            
                                            <template x-if="status === 'saving'">
                                                <div class="absolute -top-2 -right-2 w-5 h-5 bg-cyan-500 rounded-full border-2 border-white dark:border-surface-800 flex items-center justify-center text-white shadow-sm">
                                                    <i class="fa-solid fa-spinner fa-spin text-[10px]"></i>
                                                </div>
                                            </template>
                                            
                                            <template x-if="status === 'saved'">
                                                <div class="absolute -top-2 -right-2 w-5 h-5 bg-emerald-500 rounded-full border-2 border-white dark:border-surface-800 flex items-center justify-center text-white shadow-sm animate-bounce">
                                                    <i class="fa-solid fa-check text-[10px]"></i>
                                                </div>
                                            </template>
                                            
                                            <template x-if="status === 'error'">
                                                <div class="absolute -top-2 -right-2 w-5 h-5 bg-rose-500 rounded-full border-2 border-white dark:border-surface-800 flex items-center justify-center text-white shadow-sm">
                                                    <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-5 bg-gray-50/50 dark:bg-surface-850/30 border-t border-gray-100 dark:border-surface-700 mt-2 rounded-b-[2rem]">
                        <div class="w-full sm:w-auto text-sm">
                            {{ $proposals->links() }}
                        </div>
                        <button type="submit" class="w-full sm:w-auto btn-primary bg-cyan-600 hover:bg-cyan-500 text-white shadow-[0_0_15px_rgba(8,145,178,0.3)] hover:shadow-[0_0_25px_rgba(8,145,178,0.5)] border-none shrink-0 group">
                            <i class="fa-solid fa-medal mr-2 group-hover:scale-110 transition-transform"></i> Terapkan Konversi MSIB
                        </button>
                    </div>
                </form>
            @else 
                <div class="py-16 text-center">
                    <div class="w-16 h-16 bg-gray-50 dark:bg-surface-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-surface-700">
                        <i class="fa-solid fa-folder-open text-2xl text-gray-400"></i>
                    </div>
                    <p class="font-bold text-gray-900 dark:text-white">Tidak Ada Data</p>
                    <p class="text-sm text-gray-500">Belum ada mahasiswa MSIB yang bisa dikonversikan.</p>
                </div> 
            @endif
        </div>

    </div>
</x-app-layout>
