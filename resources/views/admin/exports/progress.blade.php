<x-app-layout>
    <x-slot name="title">Progress Export</x-slot>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-2 text-center">Generating Export</h2>
            <p class="text-gray-600 text-center mb-6">{{ ucfirst($export->type) }} - {{ strtoupper($export->category) }}</p>

            <div id="processing-view" class="space-y-6">
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-blue-600 bg-blue-200">
                                Progress
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold inline-block text-blue-600" id="progress-percentage">
                                0%
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-4 mb-4 text-xs flex rounded bg-blue-200">
                        <div id="progress-bar" style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-blue-600 transition-all duration-300"></div>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-gray-700 mb-2">
                        <span id="progress-records">0</span> / <span>{{ $export->total_records }}</span> records
                    </p>
                    <p class="text-sm text-gray-500" id="status-message">Memulai proses...</p>
                </div>

                <div class="flex justify-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                </div>
            </div>

            <div id="completed-view" class="hidden space-y-6">
                <div class="text-center">
                    <div class="text-6xl mb-4 text-green-500">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Export Berhasil!</h3>
                    <p class="text-gray-600 mb-4">File Anda siap untuk diunduh</p>
                </div>

                <div class="bg-gray-50 rounded p-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Filename:</span>
                        <span class="font-medium" id="filename">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Records:</span>
                        <span class="font-medium">{{ $export->total_records }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Expires:</span>
                        <span class="font-medium">24 hours</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <a id="download-btn" href="#" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded text-center">
                        <i class="fa-solid fa-download mr-2"></i> Download File
                    </a>
                    <button onclick="copyLink()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                        <i class="fa-solid fa-link mr-2"></i> Copy Link
                    </button>
                </div>
            </div>

            <div id="error-view" class="hidden space-y-6">
                <div class="text-center">
                    <div class="text-6xl mb-4 text-red-500">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Export Gagal</h3>
                    <p class="text-gray-600 mb-4" id="error-message">Terjadi kesalahan saat memproses export.</p>
                </div>
                <button onclick="window.location.reload()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fa-solid fa-arrow-rotate-right mr-2"></i> Coba Lagi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const exportId = {{ $export->id }};
const totalRecords = {{ $export->total_records }};
const CHUNK_SIZE = 100;
let collectedData = [];
let currentOffset = 0;
let isProcessing = false;

async function processNextChunk() {
    if (isProcessing) return;
    if (currentOffset >= totalRecords) {
        await finalizeExport();
        return;
    }

    isProcessing = true;
    const remaining = totalRecords - currentOffset;
    const limit = Math.min(CHUNK_SIZE, remaining);

    try {
        updateStatus(`Memproses ${currentOffset + 1}-${currentOffset + limit}...`);

        const formData = new FormData();
        formData.append('offset', currentOffset);
        formData.append('limit', limit);
        formData.append('category', '{{ $export->category }}');
        @foreach($export->filters ?? [] as $key => $value)
        @if($value)
        formData.append('{{ $key }}', '{{ $value }}');
        @endif
        @endforeach

        const response = await fetch(`/admin/exports/${exportId}/chunk`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            collectedData = collectedData.concat(result.data);
            currentOffset += limit;
            updateProgress(result.processed, totalRecords);
            
            isProcessing = false;
            await new Promise(resolve => setTimeout(resolve, 200));
            processNextChunk();
        } else {
            showError(result.error || 'Chunk processing failed');
        }

    } catch (error) {
        console.error('Chunk error:', error);
        showError(error.message);
    }
}

async function finalizeExport() {
    try {
        updateStatus('Membuat file export...');

        const response = await fetch(`/admin/exports/${exportId}/finalize`, {
            method: 'POST',
            body: JSON.stringify({ data: collectedData }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            showCompleted(result.short_code, result.filename);
        } else {
            showError(result.error || 'Finalization failed');
        }

    } catch (error) {
        console.error('Finalize error:', error);
        showError(error.message);
    }
}

function updateProgress(processed, total) {
    const percentage = Math.round((processed / total) * 100);
    document.getElementById('progress-bar').style.width = percentage + '%';
    document.getElementById('progress-percentage').textContent = percentage + '%';
    document.getElementById('progress-records').textContent = processed.toLocaleString();
}

function updateStatus(message) {
    document.getElementById('status-message').textContent = message;
}

function showCompleted(shortCode, filename) {
    document.getElementById('processing-view').classList.add('hidden');
    document.getElementById('completed-view').classList.remove('hidden');
    document.getElementById('filename').textContent = filename;
    document.getElementById('download-btn').href = `/exports/${shortCode}/download`;
    window.downloadLink = `/exports/${shortCode}/download`;
}

function showError(message) {
    document.getElementById('processing-view').classList.add('hidden');
    document.getElementById('error-view').classList.remove('hidden');
    document.getElementById('error-message').textContent = message;
}

function copyLink() {
    if (window.downloadLink) {
        const fullUrl = window.location.origin + window.downloadLink;
        navigator.clipboard.writeText(fullUrl).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateProgress(0, totalRecords);
    setTimeout(() => processNextChunk(), 500);
});
</script>
</x-app-layout>
