@extends('layouts.app')
@section('judul', 'Rekap Peminjaman')

@section('konten')
@php
    $namaBulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $totalBarang = $peminjaman->sum(
        fn ($p) => $p->items->sum('jumlah')
    );

    $periode = $bulan === 'all'
        ? 'Tahun ' . $tahun
        : ($namaBulan[(int) $bulan] ?? '') . ' ' . $tahun;

    $totalStatus = collect($jumlahStatus)->sum();

    $warnaStatus = [
        'menunggu' => '#f59e0b',
        'disetujui' => '#2563eb',
        'dipinjam' => '#6366f1',
        'dikembalikan' => '#10b981',
        'ditolak' => '#f43f5e',
    ];
@endphp

<div class="space-y-6">

    {{-- HEADER --}}
    <header class="rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-teal-500 p-6 text-white shadow-lg">
        <h1 class="mt-2 text-2xl font-bold md:text-3xl">
            Rekap Peminjaman
        </h1>

        <p class="mt-2 text-sm text-blue-50">
            Laporan transaksi peminjaman alat sekolah berdasarkan periode.
        </p>
    </header>

    {{-- FILTER --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET"
              action="{{ route('laporan.peminjaman') }}"
              class="grid grid-cols-1 gap-4 md:grid-cols-3">

            <div>
                <label for="bulan"
                       class="mb-2 block text-sm font-semibold text-black">
                    Bulan
                </label>

                <select id="bulan" name="bulan"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-black">
                    <option value="all" @selected($bulan === 'all')>
                        Semua Bulan
                    </option>

                    @foreach ($namaBulan as $nomor => $nama)
                        <option value="{{ $nomor }}"
                            @selected((string) $bulan === (string) $nomor)>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tahun"
                       class="mb-2 block text-sm font-semibold text-black">
                    Tahun
                </label>

                <select id="tahun" name="tahun"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-black">
                    @for ($t = now()->year; $t >= 2020; $t--)
                        <option value="{{ $t }}" @selected($tahun === $t)>
                            {{ $t }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full rounded-xl bg-blue-600 px-5 py-3 font-semibold text-white transition hover:bg-blue-700">
                    Tampilkan Rekap
                </button>
            </div>

        </form>
    </section>

    {{-- GRAFIK STATISTIK PEMINJAMAN --}}
    <section class="grid grid-cols-1 gap-5 lg:grid-cols-2">

        {{-- GRAFIK LINGKARAN --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">

            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-800">
                    Statistik Status Peminjaman
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Perbandingan transaksi berdasarkan status.
                </p>
            </div>

            <div class="relative mx-auto h-72 max-w-sm">
                <canvas id="grafikPeminjaman"></canvas>
            </div>
        </div>

        {{-- DETAIL STATISTIK --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">

            <h2 class="text-lg font-bold text-slate-800">
                Detail Statistik
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Jumlah transaksi setiap status peminjaman.
            </p>

            <div class="mt-5 space-y-5">

                @foreach ([
                    'menunggu' => 'Menunggu Persetujuan',
                    'disetujui' => 'Disetujui',
                    'dipinjam' => 'Sedang Dipinjam',
                    'dikembalikan' => 'Sudah Dikembalikan',
                    'ditolak' => 'Ditolak',
                ] as $status => $nama)

                    @php
                        $jumlah = $jumlahStatus[$status] ?? 0;
                        $persentase = $totalStatus > 0
                            ? ($jumlah / $totalStatus) * 100
                            : 0;
                    @endphp

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full"
                                      style="background-color: {{ $warnaStatus[$status] }}"></span>

                                <span class="text-sm font-semibold text-slate-700">
                                    {{ $nama }}
                                </span>
                            </div>

                            <span class="text-sm font-bold text-slate-900">
                                {{ $jumlah }}
                            </span>
                        </div>

                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full transition-all duration-700"
                                 style="width: {{ $persentase }}%; background-color: {{ $warnaStatus[$status] }}">
                            </div>
                        </div>
                    </div>

                @endforeach

            </div>

            <div class="mt-6 rounded-xl bg-blue-50 p-4">
                <p class="text-sm font-medium text-blue-700">
                    Total status transaksi
                </p>
                <p class="mt-1 text-3xl font-extrabold text-blue-800">
                    {{ $totalStatus }}
                </p>
            </div>
        </div>

    </section>

    {{-- TOMBOL EKSPOR --}}
    <section class="flex flex-wrap gap-3 print:hidden">
        <a href="{{ route('laporan.peminjaman.word', request()->query()) }}"
           class="rounded-xl bg-emerald-600 px-4 py-3 font-semibold text-white hover:bg-emerald-700">
            Download Word
        </a>

        <a href="{{ route('laporan.peminjaman.cetak', request()->query()) }}"
           class="rounded-xl bg-rose-600 px-4 py-3 font-semibold text-white hover:bg-rose-700">
            Cetak / Simpan PDF
        </a>
    </section>

    {{-- TABEL LAPORAN --}}
    <section id="area-laporan"
             class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">

        <div class="mb-5 text-center">
            <h2 class="text-xl font-bold text-slate-800">
                LAPORAN PEMINJAMAN ALAT SIPALA
            </h2>
            <p class="mt-1 text-sm text-slate-600">
                SMK Negeri 5 Surakarta
            </p>
            <p class="mt-1 text-sm font-semibold text-slate-700">
                Periode: {{ $periode }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left text-sm">
                <thead>
                    <tr class="bg-slate-100 text-slate-700">
                        <th class="border border-slate-300 p-2">No.</th>
                        <th class="border border-slate-300 p-2">Kode</th>
                        <th class="border border-slate-300 p-2">Peminjam</th>
                        <th class="border border-slate-300 p-2">Alat</th>
                        <th class="border border-slate-300 p-2">Jumlah</th>
                        <th class="border border-slate-300 p-2">Tgl. Pinjam</th>
                        <th class="border border-slate-300 p-2">Jatuh Tempo</th>
                        <th class="border border-slate-300 p-2">Tgl. Kembali</th>
                        <th class="border border-slate-300 p-2">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($peminjaman as $p)
                        <tr>
                            <td class="border border-slate-300 p-2">
                                {{ $loop->iteration }}
                            </td>

                            <td class="border border-slate-300 p-2 font-semibold">
                                {{ $p->kode }}
                            </td>

                            <td class="border border-slate-300 p-2">
                                {{ $p->user->name ?? '-' }}
                            </td>

                            <td class="border border-slate-300 p-2">
                                @forelse ($p->items as $item)
                                    <div>
                                        {{ $item->alat->nama
                                            ?? $item->alat->nama_alat
                                            ?? 'Alat #' . $item->alat_id }}
                                    </div>
                                @empty
                                    -
                                @endforelse
                            </td>

                            <td class="border border-slate-300 p-2">
                                {{ $p->items->sum('jumlah') }}
                            </td>

                            <td class="border border-slate-300 p-2">
                                {{ $p->tanggal_pinjam?->format('d-m-Y') ?? '-' }}
                            </td>

                            <td class="border border-slate-300 p-2">
                                {{ $p->tanggal_jatuh_tempo?->format('d-m-Y') ?? '-' }}
                            </td>

                            <td class="border border-slate-300 p-2">
                                {{ $p->tanggal_kembali?->format('d-m-Y') ?? '-' }}
                            </td>

                            <td class="border border-slate-300 p-2">
                                {{ \App\Models\Peminjaman::STATUS[$p->status] ?? $p->status }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9"
                                class="border border-slate-300 p-6 text-center text-slate-500">
                                Belum ada data peminjaman pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="mt-4 text-right text-xs text-slate-500">
            Dicetak pada {{ now()->format('d-m-Y H:i') }}
        </p>
    </section>
</div>

<style>
    @media print {
        body {
            background: white !important;
        }

        nav, aside, header, .print\:hidden {
            print-color-adjust: exact;
        }

        #area-laporan {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        #area-laporan table {
            width: 100%;
            font-size: 9px;
        }

        #area-laporan th,
        #area-laporan td {
            padding: 5px;
        }

        @page {
            size: landscape;
            margin: 10mm;
        }
    }

    #area-laporan,
    #area-laporan h2,
    #area-laporan p,
    #area-laporan table,
    #area-laporan th,
    #area-laporan td {
        color: #000000 !important;
    }

    #area-laporan table,
    #area-laporan th,
    #area-laporan td {
        border: 1px solid #000000 !important;
    }

    @media print {
    #grafikPeminjaman {
        display: block !important;
        visibility: visible !important;
    }

    #grafikPeminjaman,
    #grafikPeminjaman canvas {
        print-color-adjust: exact !important;
        -webkit-print-color-adjust: exact !important;
    }

    .grafik-container {
        display: block !important;
        visibility: visible !important;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .grafik-container canvas {
        max-width: 100% !important;
    }
}
</style>

{{-- GRAFIK CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('grafikPeminjaman');

    if (!canvas || typeof Chart === 'undefined') return;

    const dataStatus = [
        {{ $jumlahStatus['menunggu'] ?? 0 }},
        {{ $jumlahStatus['disetujui'] ?? 0 }},
        {{ $jumlahStatus['dipinjam'] ?? 0 }},
        {{ $jumlahStatus['dikembalikan'] ?? 0 }},
        {{ $jumlahStatus['ditolak'] ?? 0 }}
    ];

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: [
                'Menunggu Persetujuan',
                'Disetujui',
                'Sedang Dipinjam',
                'Sudah Dikembalikan',
                'Ditolak'
            ],
            datasets: [{
                data: dataStatus,
                backgroundColor: [
                    '#f59e0b',
                    '#2563eb',
                    '#6366f1',
                    '#10b981',
                    '#f43f5e'
                ],
                borderColor: '#ffffff',
                borderWidth: 4,
                hoverOffset: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 18
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 1200
            }
        }
    });
});
</script>
</script>

@if (!empty($otomatisCetak))
    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
@endif
@endsection
