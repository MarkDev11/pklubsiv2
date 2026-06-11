<x-app-layout>
    <x-slot name="title">Export PKL</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Export Berkas PKL</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Generate rekap PKL Magang mahasiswa bimbingan Anda.</p>
            </div>
            <a href="{{ route('dosen.nilai.pkl') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Nilai
            </a>
        </div>

        @include('partials.scoped-export-panel', [
            'exportAction' => route('dosen.exports.pkl'),
            'exportCategory' => 'pkl',
            'exportHistory' => $exportHistory,
        ])
    </div>
</x-app-layout>
