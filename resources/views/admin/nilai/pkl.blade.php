<x-app-layout>
    <x-slot name="title">Nilai PKL Magang</x-slot>
    
    <div class="space-y-6">
        
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Nilai PKL — Magang Reguler</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <i class="fa-solid fa-users text-xs mr-1"></i>
                <span class="tabular-nums">{{ $proposals->total() }}</span> mahasiswa terdaftar
            </p>
        </div>

        {{-- Search & Filter --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <form method="GET" action="{{ route(request()->route()->getName()) }}" 
                  x-data="{ 
                      search: '{{ request('search') }}',
                      submitForm() { $el.submit(); }
                  }"
                  class="flex flex-col lg:flex-row gap-3">
                {{-- Search --}}
                <div class="flex-1">
                    <div class="relative">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" name="search" x-model="search" 
                               @input.debounce.2000ms="submitForm()"
                               placeholder="Cari nama, NIM, tempat, atau mentor..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                    </div>
                </div>
                
                {{-- Filter Kelengkapan --}}
                <div class="w-full lg:w-48">
                    <select name="kelengkapan" @change="submitForm()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Semua Kelengkapan</option>
                        <option value="lengkap" {{ request('kelengkapan') == 'lengkap' ? 'selected' : '' }}>Dokumen Lengkap</option>
                        <option value="tidak_lengkap" {{ request('kelengkapan') == 'tidak_lengkap' ? 'selected' : '' }}>Dokumen Kurang</option>
                    </select>
                </div>
                
                {{-- Filter Status Nilai --}}
                <div class="w-full lg:w-48">
                    <select name="status_nilai" @change="submitForm()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="sudah" {{ request('status_nilai') == 'sudah' ? 'selected' : '' }}>Sudah Dinilai</option>
                        <option value="belum" {{ request('status_nilai') == 'belum' ? 'selected' : '' }}>Belum Dinilai</option>
                    </select>
                </div>
                
                {{-- Reset Button --}}
                @if(request()->hasAny(['search', 'kelengkapan', 'status_nilai']))
                <div class="flex gap-2">
                    <a href="{{ route(request()->route()->getName()) }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-md transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left"></i>
                        <span class="hidden sm:inline">Reset</span>
                    </a>
                </div>
                @endif
            </form>
        </div>

        {{-- Grading Table (auto-save) --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            @if($proposals->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">No</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Mahasiswa</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Tempat & Mentor</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Kelengkapan</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-32">
                                    <div>Input Nilai</div>
                                    <div class="text-[10px] font-normal normal-case text-gray-400 mt-0.5">75-100 atau kosongkan</div>
                                </th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Informasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($proposals as $i => $p)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 tabular-nums">{{ $i + 1 }}</td>

                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $p->nama }}</div>
                                    <div class="font-mono text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $p->nim }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-2">
                                        <i class="fa-solid fa-building text-gray-400 text-sm mt-0.5"></i>
                                        <div>
                                            <div class="font-medium text-gray-800 dark:text-gray-200">{{ $p->tempat_riset }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                <i class="fa-solid fa-user-tie text-xs mr-1"></i>
                                                {{ $p->nama_mentor ?? 'Belum ada mentor' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex justify-center gap-1.5">
                                        @if($p->lp)
                                            <a href="{{ route('proposal.download', ['id' => encryptUrl($p->id), 'type' => 'lp']) }}"
                                               class="px-2 py-1 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors cursor-pointer inline-flex items-center gap-1"
                                               title="Klik untuk download Laporan PKL">
                                                <i class="fa-solid fa-download text-[10px]"></i>
                                                LP
                                            </a>
                                        @else
                                            <span class="px-2 py-1 rounded text-xs font-medium border bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/30" title="Laporan PKL belum diupload">LP</span>
                                        @endif

                                        @if($p->lpp)
                                            <a href="{{ route('proposal.download', ['id' => encryptUrl($p->id), 'type' => 'lpp']) }}"
                                               class="px-2 py-1 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors cursor-pointer inline-flex items-center gap-1"
                                               title="Klik untuk download Lembar Penilaian">
                                                <i class="fa-solid fa-download text-[10px]"></i>
                                                LPP
                                            </a>
                                        @else
                                            <span class="px-2 py-1 rounded text-xs font-medium border bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/30" title="Lembar Penilaian belum diupload">LPP</span>
                                        @endif

                                        @if($p->skp)
                                            <a href="{{ route('proposal.download', ['id' => encryptUrl($p->id), 'type' => 'skp']) }}"
                                               class="px-2 py-1 rounded text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors cursor-pointer inline-flex items-center gap-1"
                                               title="Klik untuk download Sertifikat">
                                                <i class="fa-solid fa-download text-[10px]"></i>
                                                SKP
                                            </a>
                                        @else
                                            <span class="px-2 py-1 rounded text-xs font-medium border bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/30" title="Sertifikat belum diupload">SKP</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center" x-data="{
                                        id: '{{ $p->id }}',
                                        nilai: {{ $p->nilai ?: 'null' }},
                                        status: '{{ $p->nilai > 0 ? 'saved' : 'idle' }}',
                                        errorMessage: '',
                                        markPending() {
                                            if (this.nilai === null || this.nilai === '') return;
                                            this.status = 'pending';
                                        },
                                        save() {
                                            if (this.nilai === null || this.nilai === '') return;
                                            const valueToSave = this.nilai;
                                            this.status = 'saving';
                                            this.errorMessage = '';

                                            fetch('{{ route('admin.nilai.save') }}', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json'
                                                },
                                                body: JSON.stringify({ form_id: [this.id], nilai: [parseInt(valueToSave, 10)] })
                                            })
                                            .then(async res => {
                                                const data = await res.json().catch(() => ({}));
                                                if (!res.ok) {
                                                    this.status = 'error';
                                                    this.errorMessage = data?.errors?.['nilai.0']?.[0] || data?.message || 'Gagal menyimpan.';
                                                    return;
                                                }
                                                if (this.nilai == valueToSave) {
                                                    this.status = 'saved';
                                                } else {
                                                    this.status = 'pending';
                                                }
                                            })
                                            .catch(err => {
                                                this.status = 'error';
                                                this.errorMessage = 'Koneksi gagal.';
                                                console.error(err);
                                            });
                                        }
                                    }">
                                    <div class="relative w-24 mx-auto">
                                        <input type="number" name="nilai[]" x-model="nilai" min="75" max="100" step="1"
                                               @input="markPending"
                                               @input.debounce.5000ms="save"
                                               :class="{
                                                   'border-amber-400 dark:border-amber-600 bg-amber-50 dark:bg-amber-900/20 border-2 ring-4 ring-amber-500/20': status === 'pending' || status === 'saving',
                                                   'border-emerald-300 dark:border-emerald-700 bg-emerald-50 dark:bg-emerald-900/20 border-2': status === 'saved',
                                                   'border-rose-400 dark:border-rose-700 border-2': status === 'error',
                                                   'border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-blue-500': status === 'idle'
                                               }"
                                               class="w-full text-center py-2 px-2 font-semibold text-lg rounded-md border bg-white dark:bg-gray-900 text-gray-900 dark:text-white tabular-nums focus:outline-none focus:ring-2 transition-all">
                                        <template x-if="status === 'pending' || status === 'saving'">
                                            <div class="absolute -top-2 -right-2 w-5 h-5 bg-amber-500 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center text-white shadow-sm">
                                                <i class="fa-solid fa-spinner fa-spin text-[10px]"></i>
                                            </div>
                                        </template>
                                        <template x-if="status === 'saved'">
                                            <div class="absolute -top-2 -right-2 w-5 h-5 bg-emerald-500 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center text-white shadow-sm">
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            </div>
                                        </template>
                                        <template x-if="status === 'error'">
                                            <div class="absolute -top-2 -right-2 w-5 h-5 bg-rose-500 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center text-white shadow-sm" :title="errorMessage">
                                                <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                            </div>
                                        </template>
                                    </div>
                                    <p x-show="status === 'error'" x-text="errorMessage" x-cloak class="mt-1 text-[10px] text-rose-600 dark:text-rose-400"></p>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-xs space-y-1">
                                        <div class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400">
                                            <i class="fa-solid fa-user-pen text-xs"></i>
                                            <span>{{ $p->penilai ?? 'Belum dinilai' }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-gray-500 dark:text-gray-500">
                                            <i class="fa-solid fa-clock text-xs"></i>
                                            <span>{{ $p->updated_at?->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 gap-4">
                    <div class="w-full sm:w-auto">
                        {{ $proposals->links() }}
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Nilai tersimpan otomatis 5 detik setelah Anda berhenti mengetik.
                    </p>
                </div>
            @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-inbox text-2xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 font-medium">Belum ada mahasiswa PKL Magang yang terdaftar</p>
            </div>
            @endif
        </div>

    </div>
</x-app-layout>
