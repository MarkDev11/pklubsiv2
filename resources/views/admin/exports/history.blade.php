<x-app-layout>
    <x-slot name="title">Export History</x-slot>

    <div class="space-y-6 pb-10">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Export History</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Riwayat export admin dan download ulang file yang masih valid.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <form method="POST" action="{{ route('admin.exports.cleanup') }}" onsubmit="return confirm('Hapus semua export yang sudah expired beserta file storage-nya?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-sm font-medium transition-colors dark:bg-red-900/20 dark:hover:bg-red-900/30 dark:border-red-800 dark:text-red-300">
                            <i class="fa-solid fa-trash-can"></i>
                            Cleanup Expired
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.exports.cleanup-all') }}" onsubmit="return confirm('PERINGATAN: Hapus SEMUA history export dan semua file export, termasuk yang belum expired? Aksi ini tidak bisa dibatalkan.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-colors">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            Cleanup All
                        </button>
                    </form>
                    <a href="{{ route('admin.exports.pkl.config') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-download"></i>
                        Export PKL
                    </a>
                    <a href="{{ route('admin.exports.msib.config') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <i class="fa-solid fa-download"></i>
                        Export MSIB
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            @if($exports->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
                        <colgroup>
                            <col class="w-[12%]">
                            <col class="w-[34%]">
                            <col class="w-[10%]">
                            <col class="w-[12%]">
                            <col class="w-[16%]">
                            <col class="w-[16%]">
                        </colgroup>
                        <thead class="bg-gray-50 dark:bg-gray-900/60">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">File</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Records</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($exports as $export)
                                @php
                                    $isExpired = $export->status === 'completed' && $export->isExpired();
                                    $typeLabel = $export->type === 'excel' ? 'CSV' : strtoupper($export->type);
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors">
                                    <td class="px-5 py-4 align-middle whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $export->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $export->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-5 py-4 align-middle min-w-0">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $export->filename ?? '-' }}">{{ $export->filename ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ $export->category }}</div>
                                    </td>
                                    <td class="px-5 py-4 align-middle whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $export->type === 'pdf' ? 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' }}">
                                            {{ $typeLabel }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 align-middle whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($export->total_records) }}</div>
                                        @if($export->status === 'processing')
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($export->processed_records) }} processed</div>
                                        @else
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $export->download_count }} download</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 align-middle whitespace-nowrap">
                                        @if($isExpired)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                <i class="fa-solid fa-clock"></i> Expired
                                            </span>
                                        @elseif($export->status === 'completed')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-300">
                                                <i class="fa-solid fa-circle-check"></i> Completed
                                            </span>
                                        @elseif($export->status === 'failed')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300">
                                                <i class="fa-solid fa-circle-xmark"></i> Failed
                                            </span>
                                        @elseif($export->status === 'processing')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300">
                                                <i class="fa-solid fa-spinner"></i> Processing
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300">
                                                <i class="fa-solid fa-clock"></i> Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 align-middle whitespace-nowrap text-right">
                                        @if($export->status === 'completed' && !$isExpired && $export->short_code)
                                            <a href="{{ route('exports.download', $export->short_code) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors">
                                                <i class="fa-solid fa-download"></i>
                                                Download
                                            </a>
                                        @elseif($export->status === 'failed')
                                            <button type="button" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300 text-sm font-medium" title="{{ $export->error_message }}">
                                                <i class="fa-solid fa-circle-info"></i>
                                                Error
                                            </button>
                                        @else
                                            <button type="button" disabled class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-400 text-sm font-medium cursor-not-allowed">
                                                <i class="fa-solid fa-ban"></i>
                                                Unavailable
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $exports->links() }}
                </div>
            @else
                <div class="text-center py-16 px-6">
                    <div class="w-14 h-14 mx-auto rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 mb-4">
                        <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Belum Ada Export</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Mulai export data dari menu Export PKL atau Export MSIB.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
