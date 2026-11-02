<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Peminjaman SIPALA</title>
</head>
<body>
@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $periode = $bulan === 'all'
        ? 'Tahun ' . $tahun
        : ($namaBulan[(int) $bulan] ?? '') . ' ' . $tahun;
@endphp

<h2 style="text-align:center;">LAPORAN PEMINJAMAN ALAT SIPALA</h2>
<p style="text-align:center;">SMK Negeri 5 Surakarta</p>
<p style="text-align:center;">Periode: {{ $periode }}</p>

<table width="100%" border="1" cellspacing="0" cellpadding="5">
    <thead>
        <tr>
            <th>No.</th>
            <th>Kode</th>
            <th>Peminjam</th>
            <th>Alat</th>
            <th>Jumlah</th>
            <th>Tanggal Pinjam</th>
            <th>Jatuh Tempo</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($peminjaman as $p)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $p->kode }}</td>
                <td>{{ $p->user->name ?? '-' }}</td>
                <td>
                    @forelse ($p->items as $item)
                        {{ $item->alat->nama
                            ?? $item->alat->nama_alat
                            ?? 'Alat #' . $item->alat_id }}
                        @if (!$loop->last), @endif
                    @empty
                        -
                    @endforelse
                </td>
                <td>{{ $p->items->sum('jumlah') }}</td>
                <td>{{ $p->tanggal_pinjam?->format('d-m-Y') ?? '-' }}</td>
                <td>{{ $p->tanggal_jatuh_tempo?->format('d-m-Y') ?? '-' }}</td>
                <td>{{ $p->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td>
                <td>{{ \App\Models\Peminjaman::STATUS[$p->status] ?? $p->status }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9">Belum ada data peminjaman pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<p>Total transaksi: {{ $peminjaman->count() }}</p>
<p>Total barang: {{ $peminjaman->sum(fn ($p) => $p->items->sum('jumlah')) }}</p>
<p>Dicetak pada: {{ now()->format('d-m-Y H:i') }}</p>
</body>
</html>
