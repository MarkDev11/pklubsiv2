<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai MSIB</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        h2 { text-align: center; margin-bottom: 5px; }
        h4 { text-align: center; margin-top: 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: center; }
        th { background-color: #059669; color: white; font-size: 10px; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .footer { margin-top: 20px; text-align: right; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h2>Rekap Nilai Mahasiswa MSIB/PMK</h2>
    <h4>Sistem PKL — Universitas Bina Sarana Informatika</h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Judul PKL</th>
                <th>Tempat Riset</th>
                <th>Nama Mentor</th>
                <th>Email Perusahaan</th>
                <th>Dosen PA</th>
                <th>Nilai</th>
                <th>Dinilai Oleh</th>
                <th>Terakhir Update</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proposals as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->nim }}</td>
                    <td>{{ $p->nama }}</td>
                    <td style="text-align:left">{{ $p->judul_pkl }}</td>
                    <td>{{ $p->tempat_riset }}</td>
                    <td>{{ $p->nama_mentor }}</td>
                    <td>{{ $p->email_perusahaan }}</td>
                    <td>{{ $p->dosen_pa }}</td>
                    <td><strong>{{ $p->nilai }}</strong></td>
                    <td>{{ $p->penilai ?? '-' }}</td>
                    <td>{{ $p->updated_at?->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="11">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ $user->name }} — {{ now()->format('d F Y H:i') }}
    </div>
</body>
</html>
