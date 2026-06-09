<x-app-layout>
    <x-slot name="title">System Error Logs</x-slot>

    <div class="space-y-6 animate-fade-in pb-10">
        
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/10 text-red-600 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-bug text-xl"></i>
                    </div>
                    System Error Logs
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pemantauan otomatis terhadap kegagalan sistem dan *exceptions* secara *real-time*.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.logs.error.clear') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus SEMUA catatan log error? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger inline-flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-trash-can"></i> Bersihkan Semua Log
                    </button>
                </form>
            </div>
        </div>

        {{-- Log Statistics / Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-red-50 dark:bg-red-500/10 text-red-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Insiden</h4>
                    <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $logs->total() }}</p>
                </div>
            </div>
        </div>

        {{-- Data Table Module --}}
        <div class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative">
            
            @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border-b border-emerald-100 dark:border-emerald-800 p-4 flex items-center gap-3 text-emerald-600 dark:text-emerald-400">
                <i class="fa-solid fa-circle-check"></i>
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
            @endif

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-widest font-bold">
                        <tr>
                            <th class="px-6 py-4 rounded-tl-xl">Waktu & Tipe</th>
                            <th class="px-6 py-4">Pesan Error</th>
                            <th class="px-6 py-4">User Status</th>
                            <th class="px-6 py-4">File Location</th>
                            <th class="px-6 py-4 text-right rounded-tr-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-surface-800/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-8 rounded-full bg-red-500"></div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-500 mb-0.5"><i class="fa-regular fa-clock mr-1"></i>{{ $log->created_at->format('d M / H:i:s') }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ str_contains($log->type, 'HttpException') ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                            {{ class_basename($log->type) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs">
                                <p class="font-semibold text-gray-900 dark:text-gray-200 line-clamp-2" title="{{ $log->message }}">
                                    {{ $log->message }}
                                </p>
                                @if($log->request_url)
                                    <p class="text-xs text-indigo-500 font-mono mt-1 line-clamp-1"><i class="fa-solid fa-link mr-1"></i>{{ Str::limit($log->request_url, 40) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($log->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-user text-[10px] text-gray-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800 dark:text-gray-200 text-xs">{{ $log->user->name }}</p>
                                        <p class="text-[10px] text-gray-500">Role: {{ $log->user->role?->value ?? '-' }}</p>
                                    </div>
                                </div>
                                @else
                                <span class="text-xs text-gray-400 italic">Guest / No Auth</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 max-w-[200px]">
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    <p class="truncate font-mono" title="{{ $log->file }}">{{ basename($log->file) }}</p>
                                    <p class="font-bold text-gray-700 dark:text-gray-300 mt-0.5">Line: {{ $log->line }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="document.getElementById('modal-{{ $log->id }}').classList.remove('hidden')" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 flex items-center justify-center transition-colors" title="Lihat Detail Stack Trace">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <form action="{{ route('admin.logs.error.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Hapus log ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 flex items-center justify-center transition-colors" title="Hapus Log">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal for Stack Trace --}}
                        <div id="modal-{{ $log->id }}" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                            <div class="fixed inset-0 bg-gray-900/50 dark:bg-black/60 backdrop-blur-sm transition-opacity"></div>
                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                <div class="flex min-h-full items-center justify-center p-4 sm:p-0">
                                    <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 w-full max-w-4xl border border-gray-100 dark:border-gray-700">
                                        
                                        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                            <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2" id="modal-title">
                                                <i class="fa-solid fa-bug text-red-500"></i> Detail Kesalahan Lanjutan
                                            </h3>
                                            <button onclick="document.getElementById('modal-{{ $log->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                                                <i class="fa-solid fa-xmark text-lg"></i>
                                            </button>
                                        </div>
                                        
                                        <div class="px-6 py-5 space-y-6">
                                            <div>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Pesan Error Utama</p>
                                                <div class="bg-red-50 dark:bg-red-900/10 p-3 rounded-lg border border-red-100 dark:border-red-800/30 text-red-700 dark:text-red-400 text-sm font-semibold font-mono break-words">
                                                    {{ $log->message }}
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Exception Class</p>
                                                    <p class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $log->type }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Target Endpoint / URL</p>
                                                    <p class="text-sm font-mono text-indigo-600 dark:text-indigo-400 break-all">{{ $log->request_url ?: 'N/A' }}</p>
                                                </div>
                                            </div>

                                            <div>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 flex items-center justify-between">
                                                    <span>Stack Trace Data</span>
                                                    <span class="text-[10px] bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded font-mono">{{ basename($log->file) }}:{{ $log->line }}</span>
                                                </p>
                                                <div class="bg-gray-900 rounded-xl p-4 overflow-x-auto mt-1 custom-scrollbar">
                                                    <pre class="text-xs text-gray-300 font-mono whitespace-pre-wrap leading-relaxed">{{ $log->trace }}</pre>
                                                </div>
                                            </div>
                                            
                                            @if($log->payload && $log->payload !== '[]' && $log->payload !== '{}')
                                            <div>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Request Payload</p>
                                                <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4 border border-gray-200 dark:border-gray-700 overflow-x-auto">
                                                    <pre class="text-xs text-gray-800 dark:text-gray-300 font-mono">{{ json_encode(json_decode($log->payload), JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-shield-check text-2xl text-emerald-500"></i>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Sistem Terpantau Aman</h3>
                                <p class="text-sm text-gray-500">Tidak ada kegagalan fungsi atau error *exception* yang tercatat.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-surface-850/20">
                {{ $logs->links() }}
            </div>
        </div>

    </div>
</x-app-layout>
