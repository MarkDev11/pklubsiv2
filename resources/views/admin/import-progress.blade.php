<x-app-layout>
    <x-slot name="title">Progress Import</x-slot>

    <div class="container mx-auto px-4 py-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-8">
                <h2 class="text-2xl font-bold mb-2 text-center text-gray-900 dark:text-white">Memproses Import</h2>
                <p class="text-gray-600 dark:text-gray-400 text-center mb-6">{{ $import->filename }}</p>

                <div id="processing-view" class="space-y-6">
                    <div>
                        <div class="flex mb-2 items-center justify-between">
                            <span class="text-xs font-semibold py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-300">Progress</span>
                            <span class="text-xs font-semibold text-blue-600 dark:text-blue-300" id="progress-percentage">{{ $import->getProgressPercentage() }}%</span>
                        </div>
                        <div class="overflow-hidden h-4 mb-4 text-xs flex rounded bg-blue-100 dark:bg-blue-900/30">
                            <div id="progress-bar" style="width:{{ $import->getProgressPercentage() }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600 transition-all duration-300"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-lg bg-gray-50 dark:bg-gray-900 p-3">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Processed</div>
                            <div class="font-semibold text-gray-900 dark:text-white"><span id="processed-count">{{ $import->processed_rows }}</span> / {{ $import->total_rows }}</div>
                        </div>
                        <div class="rounded-lg bg-emerald-50 dark:bg-emerald-900/20 p-3">
                            <div class="text-xs text-emerald-600 dark:text-emerald-300">Imported</div>
                            <div class="font-semibold text-emerald-700 dark:text-emerald-300" id="imported-count">{{ $import->imported_count }}</div>
                        </div>
                        <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 p-3">
                            <div class="text-xs text-amber-600 dark:text-amber-300">Skipped</div>
                            <div class="font-semibold text-amber-700 dark:text-amber-300" id="skipped-count">{{ $import->skipped_count }}</div>
                        </div>
                    </div>

                    <p class="text-sm text-gray-500 dark:text-gray-400 text-center" id="status-message">Memulai proses...</p>

                    <div class="flex justify-center">
                        <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600"></div>
                    </div>
                </div>

                <div id="completed-view" class="hidden space-y-6 text-center">
                    <div class="text-5xl text-emerald-500"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Import Selesai</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-1"><span id="completed-imported">0</span> akun masuk, <span id="completed-skipped">0</span> row skip.</p>
                    </div>
                    <a href="{{ route('admin.akun.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                        <i class="fa-solid fa-users-gear"></i>
                        Lihat Akun
                    </a>
                </div>

                <div id="error-view" class="hidden space-y-4 text-center">
                    <div class="text-5xl text-red-500"><i class="fa-solid fa-circle-xmark"></i></div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Import Gagal</h3>
                    <p class="text-gray-600 dark:text-gray-400" id="error-message">Terjadi kesalahan.</p>
                    <button onclick="window.location.reload()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                        <i class="fa-solid fa-arrow-rotate-right"></i>
                        Coba Lagi
                    </button>
                </div>
            </div>

            @if($import->skip_details)
                <div class="mt-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <h4 class="font-semibold text-sm text-gray-900 dark:text-white mb-3">Contoh Row Skip</h4>
                    <div class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
                        @foreach(array_slice($import->skip_details, 0, 10) as $detail)
                            <div>Row {{ $detail['row'] ?? '-' }}: {{ $detail['username'] ?? '-' }} - {{ $detail['reason'] ?? '-' }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        const importId = {{ $import->id }};
        const totalRows = {{ $import->total_rows }};
        const chunkSize = 100;
        let processedRows = {{ $import->processed_rows }};
        let isProcessing = false;

        async function processNextChunk() {
            if (isProcessing) return;
            if (processedRows >= totalRows) {
                await finalizeImport();
                return;
            }

            isProcessing = true;
            updateStatus(`Memproses ${processedRows + 1}-${Math.min(processedRows + chunkSize, totalRows)}...`);

            try {
                const formData = new FormData();
                formData.append('limit', chunkSize);

                const response = await fetch(`/admin/import/${importId}/chunk`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (!result.success) {
                    showError(result.error || 'Chunk import gagal.');
                    return;
                }

                processedRows = result.processed;
                updateProgress(result.processed, result.total, result.imported, result.skipped);
                isProcessing = false;

                await new Promise(resolve => setTimeout(resolve, 150));
                processNextChunk();
            } catch (error) {
                showError(error.message);
            }
        }

        async function finalizeImport() {
            updateStatus('Menyelesaikan import...');

            try {
                const response = await fetch(`/admin/import/${importId}/finalize`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                if (!result.success) {
                    showError(result.error || 'Finalize import gagal.');
                    return;
                }

                showCompleted();
            } catch (error) {
                showError(error.message);
            }
        }

        function updateProgress(processed, total, imported, skipped) {
            const percentage = total === 0 ? 100 : Math.round((processed / total) * 100);
            document.getElementById('progress-bar').style.width = percentage + '%';
            document.getElementById('progress-percentage').textContent = percentage + '%';
            document.getElementById('processed-count').textContent = processed.toLocaleString();
            document.getElementById('imported-count').textContent = imported.toLocaleString();
            document.getElementById('skipped-count').textContent = skipped.toLocaleString();
            document.getElementById('completed-imported').textContent = imported.toLocaleString();
            document.getElementById('completed-skipped').textContent = skipped.toLocaleString();
        }

        function updateStatus(message) {
            document.getElementById('status-message').textContent = message;
        }

        function showCompleted() {
            document.getElementById('processing-view').classList.add('hidden');
            document.getElementById('completed-view').classList.remove('hidden');
        }

        function showError(message) {
            isProcessing = false;
            document.getElementById('processing-view').classList.add('hidden');
            document.getElementById('error-view').classList.remove('hidden');
            document.getElementById('error-message').textContent = message;
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (totalRows === 0) {
                showCompleted();
                return;
            }

            setTimeout(() => processNextChunk(), 500);
        });
    </script>
</x-app-layout>
