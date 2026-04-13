<x-app-layout>
    <x-slot name="title">Penilaian PKL</x-slot>
    
    <div class="space-y-6 animate-fade-in pb-10" x-data="{ search: '' }">

        {{-- Page Header --}}
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-file-signature text-xl"></i>
                    </div>
                    Input Nilai PKL (Magang)
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Berikan penilaian teknis untuk mahasiswa bimbingan institusi Anda.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1 sm:flex-initial">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400"></i>
                    </div>
                    <input type="text" x-model="search" placeholder="Cari mahasiswa..." class="pl-10 pr-4 py-2 w-full sm:w-64 bg-white dark:bg-surface-800 border border-gray-200 dark:border-surface-700 rounded-xl text-sm focus:ring-amber-500 focus:border-amber-500 dark:text-white shadow-sm transition-all h-full outline-none">
                </div>
                
                <a href="{{ route('mentor.pdf.pkl') }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-surface-700 border border-gray-200 dark:border-surface-600 text-gray-700 dark:text-gray-200 text-sm font-bold hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    <i class="fa-solid fa-file-pdf text-rose-500"></i>
                    <span>Cetak Rekap</span>
                </a>
            </div>
        </div>

        @if(isset($openingHours) && !$openingHours->isNilaiBuka())
            <div class="flex items-center gap-4 p-5 rounded-3xl bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-800/30 text-rose-600 dark:text-rose-400 animate-fade-in shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-lock text-xl"></i>
                </div>
                <div>
                    <p class="font-black text-sm uppercase tracking-wider">Akses Penilaian Ditutup</p>
                    <p class="text-sm opacity-80 font-medium">Masa penginputan nilai telah berakhir atau belum dibuka oleh Admin.</p>
                </div>
            </div>
        @elseif($proposals->count())
            <form method="POST" action="{{ route('mentor.nilai.save') }}" class="space-y-4">
                @csrf
                <div class="bg-white dark:bg-surface-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-surface-700 overflow-hidden relative">
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-surface-900 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest font-bold">
                                <tr>
                                    <th class="px-6 py-4 rounded-tl-xl w-16">No</th>
                                    <th class="px-6 py-4">Mahasiswa</th>
                                    <th class="px-6 py-4">Status Berkas</th>
                                    <th class="px-6 py-4 text-center">Skor Akhir (0-100)</th>
                                    <th class="px-6 py-4">Keterangan</th>
                                    <th class="px-6 py-4 rounded-tr-xl">Updated</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-surface-700">
                                @foreach($proposals as $i => $p)
                                <tr x-show="search === '' || $el.dataset.search.includes(search.toLowerCase())" data-search="{{ strtolower(e($p->nama . ' ' . $p->nim)) }}" class="hover:bg-gray-50/50 dark:hover:bg-surface-800/50 transition-colors group">
                                    <td class="px-6 py-4 text-center font-bold text-gray-400 font-mono">{{ $i+1 }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100 dark:border-indigo-800/30">
                                                <i class="fa-solid fa-user-graduate"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 dark:text-white">{{ $p->nama }}</p>
                                                <p class="text-xs font-mono text-gray-500 mt-0.5">{{ $p->nim }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1.5 grayscale group-hover:grayscale-0 transition-all duration-500">
                                            <span title="Laporan PKL" class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-black border {{ $p->lp ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-rose-50 text-rose-300 border-rose-100' }}">LP</span>
                                            <span title="Lembar Penilaian" class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-black border {{ $p->lpp ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-rose-50 text-rose-300 border-rose-100' }}">LPP</span>
                                            <span title="Sertifikat/SKP" class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-black border {{ $p->skp ? 'bg-emerald-50 text-emerald-600 border-emerald-200' : 'bg-rose-50 text-rose-300 border-rose-100' }}">SKP</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center" x-data="{ 
                                            id: '{{ $p->id }}', 
                                            nilai: {{ $p->nilai ?: 'null' }}, 
                                            status: '{{ $p->nilai > 0 ? 'saved' : 'idle' }}',
                                            save() {
                                                if(this.nilai === null || this.nilai === '') return;
                                                this.status = 'saving';
                                                
                                                fetch('{{ route('mentor.nilai.save') }}', {
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
                                                });
                                            }
                                        }">
                                        <div class="flex justify-center">
                                            <input type="hidden" name="form_id[]" value="{{ $p->id }}">
                                            <div class="relative w-24 group/input">
                                                <input type="number" name="nilai[]" x-model="nilai" min="0" max="100" @input.debounce.5000ms="save"
                                                    :class="{'bg-emerald-50 border-emerald-200 text-emerald-700 shadow-inner': status === 'saved', 'bg-amber-50 border-amber-200 text-amber-700': status !== 'saved'}"
                                                    class="block w-full py-2.5 px-3 text-center text-lg font-black rounded-2xl transition-all border-2 focus:ring-4 focus:ring-amber-500/20 focus:border-amber-500 outline-none"
                                                >
                                                
                                                <template x-if="status === 'saving'">
                                                    <div class="absolute -top-2 -right-2 w-5 h-5 bg-amber-500 rounded-full border-2 border-white dark:border-surface-800 flex items-center justify-center text-white shadow-sm">
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
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($p->penilai)
                                            <div class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                                Oleh: {{ $p->penilai }}
                                            </div>
                                        @else
                                            <span class="text-[10px] text-gray-300 italic font-medium">Belum dinilai</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs font-mono text-gray-400 group-hover:text-gray-600 transition-colors">
                                        {{ $p->updated_at?->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Bulk Action Bar --}}
                <div class="flex flex-col sm:flex-row justify-between items-center bg-white/50 dark:bg-surface-800/50 backdrop-blur-md p-4 rounded-3xl border border-white/20 shadow-lg mt-6">
                    <div class="text-sm text-gray-500 font-medium mb-4 sm:mb-0">
                        {{ $proposals->links() }}
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-2xl shadow-xl shadow-amber-500/20 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        Simpan Semua Nilai
                    </button>
                </div>
            </form>
        @else
            <div class="bg-white dark:bg-surface-800 rounded-[2rem] p-20 text-center border border-dashed border-gray-200 dark:border-surface-700">
                <div class="w-20 h-20 bg-gray-50 dark:bg-surface-900 rounded-3xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <i class="fa-solid fa-folder-open text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tidak Ada Mahasiswa</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-1 max-w-xs mx-auto text-sm">Belum ada mahasiswa bimbingan Magang yang didaftarkan untuk perusahaan Anda.</p>
            </div>
        @endif
    </div>
</x-app-layout>
