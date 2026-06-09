<x-app-layout>
    <x-slot name="title">Mahasiswa Belum Dinilai</x-slot>

    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Mahasiswa Belum Dinilai</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <i class="fa-solid fa-users text-xs mr-1"></i>
                <span class="tabular-nums">{{ $proposals->total() }}</span> mahasiswa menunggu penilaian
            </p>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            @if($proposals->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider w-16">No</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Mahasiswa</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Tempat Riset</th>
                                <th class="px-4 py-3 text-center font-medium text-gray-600 dark:text-gray-400 uppercase text-xs tracking-wider">Jenis</th>
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
                                    <div class="font-medium text-gray-800 dark:text-gray-200 truncate max-w-[240px]">
                                        <i class="fa-solid fa-building text-gray-400 text-xs mr-1"></i> {{ $p->tempat_riset }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/30">{{ $p->jns_pkl }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                    {{ $proposals->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-check-double text-2xl text-emerald-500"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Semua mahasiswa sudah dinilai.</p>
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
