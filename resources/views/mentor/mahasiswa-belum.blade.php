<x-app-layout>
    <x-slot name="title">Mahasiswa Belum Dinilai</x-slot>
    <div class="space-y-6 animate-fade-in">
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">⚠️ Mahasiswa Belum Dinilai</h3>
            @if($proposals->count())
            <div class="table-container"><table>
                <thead><tr><th>No</th><th>NIM</th><th>Nama</th><th>Tempat</th><th>Jenis</th></tr></thead>
                <tbody>@foreach($proposals as $i => $p)<tr>
                    <td>{{ $i+1 }}</td><td>{{ $p->nim }}</td><td>{{ $p->nama }}</td><td>{{ $p->tempat_riset }}</td>
                    <td><span class="badge-blue">{{ $p->jns_pkl }}</span></td>
                </tr>@endforeach</tbody>
            </table></div>
            <div class="mt-4 text-sm">
                {{ $proposals->links() }}
            </div>
            @else <p class="text-center text-gray-500 py-8">Semua sudah dinilai! 🎉</p> @endif
        </div>
    </div>
</x-app-layout>
