<x-app-layout>
    <x-slot name="title">Konfigurasi Export {{ strtoupper($category) }}</x-slot>

<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-2">Konfigurasi Export {{ strtoupper($category) }}</h1>
        <p class="text-gray-600 mb-6">Pilih filter untuk data yang akan di-export</p>

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
        @endif

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Records yang Akan Di-export:</p>
                    <p class="text-3xl font-bold text-blue-600" id="record-count">-</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Maksimal:</p>
                    <p class="text-xl font-bold text-gray-800">1000 records</p>
                </div>
            </div>
            <div id="count-message" class="mt-3 hidden"></div>
        </div>

        <form id="export-form" method="POST" action="{{ route('admin.exports.initiate') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="category" value="{{ $category }}">

            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h3 class="text-lg font-semibold mb-4">Filter Data</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Dosen Pembimbing Akademik</label>
                        <select name="dosen_pa" class="filter-input w-full border rounded px-3 py-2">
                            <option value="">-- Semua Dosen --</option>
                            @foreach($dosens as $dosen)
                            <option value="{{ $dosen->username }}">{{ $dosen->name }} (NIP: {{ $dosen->username }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Status Penilaian</label>
                        <select name="status_nilai" class="filter-input w-full border rounded px-3 py-2">
                            <option value="">-- Semua Status --</option>
                            <option value="belum_dinilai">Belum Dinilai</option>
                            <option value="sudah_dinilai">Sudah Dinilai</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Nilai Minimal</label>
                        <input type="number" name="nilai_min" min="0" max="100" 
                               class="filter-input w-full border rounded px-3 py-2" placeholder="0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Nilai Maksimal</label>
                        <input type="number" name="nilai_max" min="0" max="100" 
                               class="filter-input w-full border rounded px-3 py-2" placeholder="100">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Mentor</label>
                        <select name="mentor" class="filter-input w-full border rounded px-3 py-2">
                            <option value="">-- Semua Mentor --</option>
                            @foreach($mentors as $mentor)
                            <option value="{{ $mentor->name }}">{{ $mentor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Tempat Riset</label>
                        <input type="text" name="tempat_riset" 
                               class="filter-input w-full border rounded px-3 py-2" 
                               placeholder="Cari nama perusahaan...">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Cari Mahasiswa (Nama/NIM)</label>
                        <input type="text" name="search" 
                               class="filter-input w-full border rounded px-3 py-2" 
                               placeholder="Nama atau NIM...">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Tipe Export</h3>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="type" value="pdf" checked class="mr-2">
                        <span>PDF</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="type" value="excel" class="mr-2">
                        <span>Excel (CSV)</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-between">
                <button type="button" id="reset-btn" class="px-4 py-2 border rounded hover:bg-gray-50">
                    Reset Filter
                </button>
                <button type="submit" id="submit-btn" disabled
                        class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed">
                    Generate Export
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('export-form');
    const filterInputs = document.querySelectorAll('.filter-input');
    const countDisplay = document.getElementById('record-count');
    const countMessage = document.getElementById('count-message');
    const submitBtn = document.getElementById('submit-btn');
    const resetBtn = document.getElementById('reset-btn');
    
    const MAX_RECORDS = 1000;
    let debounceTimer;
    
    filterInputs.forEach(input => {
        input.addEventListener('change', updateCount);
        input.addEventListener('input', debounceUpdateCount);
    });
    
    function debounceUpdateCount() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(updateCount, 500);
    }
    
    async function updateCount() {
        countDisplay.textContent = '...';
        submitBtn.disabled = true;
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch('{{ route("admin.exports.count") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            const count = data.count;
            
            countDisplay.textContent = count.toLocaleString();
            countMessage.classList.remove('hidden');
            
            if (count === 0) {
                countMessage.className = 'mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded text-yellow-800';
                countMessage.textContent = 'Tidak ada data yang sesuai filter. Sesuaikan filter Anda.';
                submitBtn.disabled = true;
            } else if (count > MAX_RECORDS) {
                countMessage.className = 'mt-3 p-3 bg-red-50 border border-red-200 rounded text-red-800';
                countMessage.textContent = `Terlalu banyak data (${count.toLocaleString()} records). Maksimal ${MAX_RECORDS}. Gunakan filter lebih spesifik.`;
                submitBtn.disabled = true;
            } else {
                countMessage.className = 'mt-3 p-3 bg-green-50 border border-green-200 rounded text-green-800';
                countMessage.textContent = `Siap export ${count.toLocaleString()} records.`;
                submitBtn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            countDisplay.textContent = 'Error';
            countMessage.className = 'mt-3 p-3 bg-red-50 border border-red-200 rounded text-red-800';
            countMessage.textContent = 'Terjadi kesalahan saat menghitung data.';
        }
    }
    
    resetBtn.addEventListener('click', function() {
        form.reset();
        updateCount();
    });
    
    updateCount();
});
</script>
</x-app-layout>
