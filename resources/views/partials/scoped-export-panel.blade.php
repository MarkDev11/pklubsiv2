<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
        <div class="flex items-start justify-between gap-4 mb-5">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Export Berkas</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Generate file {{ strtoupper($exportCategory) }}. Link download aktif 1 jam.</p>
            </div>
            <div class="w-10 h-10 rounded-md bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                <i class="fa-solid fa-file-export"></i>
            </div>
        </div>

        <form method="POST" action="{{ $exportAction }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 cursor-pointer hover:border-blue-300 dark:hover:border-blue-600 transition-colors">
                    <input type="radio" name="type" value="pdf" checked class="text-blue-600 focus:ring-blue-500">
                    <span class="flex items-center gap-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                        <i class="fa-solid fa-file-pdf text-red-500"></i>
                        PDF
                    </span>
                </label>
                <label class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 cursor-pointer hover:border-blue-300 dark:hover:border-blue-600 transition-colors">
                    <input type="radio" name="type" value="excel" class="text-blue-600 focus:ring-blue-500">
                    <span class="flex items-center gap-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                        <i class="fa-solid fa-file-csv text-emerald-500"></i>
                        Excel (CSV)
                    </span>
                </label>
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors">
                <i class="fa-solid fa-download"></i>
                Generate Export
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Riwayat Export</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">10 export {{ strtoupper($exportCategory) }} terbaru.</p>
            </div>
            <i class="fa-solid fa-clock-rotate-left text-gray-400"></i>
        </div>

        @if(($exportHistory ?? collect())->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">File</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($exportHistory as $export)
                            @php $isExpired = $export->status === 'completed' && $export->isExpired(); @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                <td class="px-4 py-3 min-w-0">
                                    <div class="font-medium text-gray-900 dark:text-white truncate max-w-[260px]" title="{{ $export->filename ?? '-' }}">{{ $export->filename ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $export->created_at->format('d/m/Y H:i') }} · {{ strtoupper($export->type === 'excel' ? 'CSV' : $export->type) }} · {{ $export->total_records }} data</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($isExpired)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                            <i class="fa-solid fa-clock"></i> Expired
                                        </span>
                                    @elseif($export->status === 'completed')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300">
                                            <i class="fa-solid fa-circle-check"></i> Ready
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300">
                                            <i class="fa-solid fa-circle-xmark"></i> Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    @if($export->status === 'completed' && !$isExpired)
                                        <a href="{{ route('exports.file.download', $export) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors">
                                            <i class="fa-solid fa-download"></i>
                                            Download
                                        </a>
                                    @else
                                        <button type="button" disabled class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-400 text-xs font-semibold cursor-not-allowed">
                                            <i class="fa-solid fa-ban"></i>
                                            Expired
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-10 text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 mb-3">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Belum ada riwayat export.</p>
            </div>
        @endif
    </div>
</div>
