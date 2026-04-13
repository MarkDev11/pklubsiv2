<x-app-layout>
    <x-slot name="title">Nilai MSIB</x-slot>
    <div class="space-y-6 animate-fade-in">
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">📝 Input Nilai — MSIB/PMK</h3>
            @if(isset($openingHours) && !$openingHours->isNilaiBuka())
                <div class="alert-error">Input nilai sedang ditutup.</div>
            @elseif($proposals->count())
            <form method="POST" action="{{ route('mentor.nilai.save') }}">
                @csrf
                <div class="table-container"><table>
                    <thead><tr><th>No</th><th>NIM</th><th>Nama</th><th>Tempat</th><th>Laporan</th><th>Nilai</th><th>Penilai</th><th>Updated</th></tr></thead>
                    <tbody>@foreach($proposals as $i => $p)<tr>
                        <td>{{ $i+1 }}</td><td>{{ $p->nim }}</td><td>{{ $p->nama }}</td><td>{{ $p->tempat_riset }}</td>
                        <td><span class="badge {{ $p->lp ? 'badge-green' : 'badge-red' }}">LP</span> <span class="badge {{ $p->lpp ? 'badge-green' : 'badge-red' }}">LPP</span> <span class="badge {{ $p->skp ? 'badge-green' : 'badge-red' }}">SKP</span></td>
                        <td x-data="{ 
                                id: '{{ $p->id }}', 
                                nilai: {{ $p->nilai ?: 'null' }}, 
                                status: '{{ $p->nilai > 0 ? 'saved' : 'idle' }}',
                                save() {
                                    if(this.nilai === null || this.nilai === '') return;
                                    this.status = 'saving';
                                    fetch('{{ route('mentor.nilai.save') }}', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                                        body: JSON.stringify({ form_id: [this.id], nilai: [this.nilai] })
                                    }).then(r => r.json()).then(d => { this.status = 'saved'; }).catch(e => { this.status = 'error'; });
                                }
                            }">
                            <input type="hidden" name="form_id[]" value="{{ $p->id }}">
                            <div class="relative w-20">
                                <input type="number" name="nilai[]" x-model="nilai" min="0" max="100" @input.debounce.5000ms="save" class="form-input w-20 text-center">
                                <span x-show="status === 'saving'" class="absolute -top-2 -right-2 text-[10px] bg-amber-500 text-white rounded-full w-4 h-4 flex items-center justify-center"><i class="fa-solid fa-spinner fa-spin"></i></span>
                                <span x-show="status === 'saved'" class="absolute -top-2 -right-2 text-[10px] bg-emerald-500 text-white rounded-full w-4 h-4 flex items-center justify-center"><i class="fa-solid fa-check"></i></span>
                            </div>
                        </td>
                        <td class="text-xs text-gray-500">{{ $p->penilai ?? '-' }}</td>
                        <td class="text-xs text-gray-400">{{ $p->updated_at?->format('d/m/Y H:i') }}</td>
                    </tr>@endforeach</tbody>
                </table></div>
                <div class="flex flex-col sm:flex-row justify-between items-center sm:items-end mt-4 gap-4">
                    <div class="w-full sm:w-auto text-sm">
                        {{ $proposals->links() }}
                    </div>
                    <button type="submit" class="btn-success shrink-0">Simpan Nilai</button>
                </div>
            </form>
            @else <p class="text-center text-gray-500 py-8">Tidak ada data.</p> @endif
        </div>
    </div>
</x-app-layout>
