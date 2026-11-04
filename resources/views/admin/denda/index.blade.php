@extends('layouts.app')
@section('judul', 'Kelola Denda')

@section('konten')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-teal-600">SIPALA / ADMIN</p>
            <h1 class="text-2xl font-bold text-slate-800">Kelola Denda</h1>
            <p class="text-sm text-slate-500 mt-1">
                Pantau keterlambatan dan catat pembayaran denda peminjaman alat.
            </p>
        </div>

        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
            <i class="fas fa-arrow-left"></i>
            Kembali ke Dashboard
        </a>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- RINGKASAN --}}
    @php
        $totalBelumLunas = $peminjamans->getCollection()
            ->filter(fn($p) => $p->status_denda !== 'lunas')
            ->sum(fn($p) => $p->status === 'dikembalikan'
                ? $p->total_denda
                : $p->hitungDenda());

        $totalLunas = $peminjamans->getCollection()
            ->where('status_denda', 'lunas')
            ->sum('total_denda');

        $jumlahTerlambat = $peminjamans->getCollection()
            ->filter(fn($p) => $p->status === 'dipinjam')
            ->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="rounded-2xl bg-white border border-slate-100 p-5 shadow-sm">
            <p class="text-sm text-slate-500">Peminjaman Terlambat</p>
            <h2 class="text-2xl font-bold text-orange-600 mt-2">
                {{ $jumlahTerlambat }}
            </h2>
            <p class="text-xs text-slate-400 mt-1">Belum dikembalikan</p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-100 p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Denda Belum Lunas</p>
            <h2 class="text-2xl font-bold text-red-600 mt-2">
                Rp{{ number_format($totalBelumLunas, 0, ',', '.') }}
            </h2>
            <p class="text-xs text-slate-400 mt-1">Termasuk denda berjalan</p>
        </div>

        <div class="rounded-2xl bg-white border border-slate-100 p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Denda Lunas</p>
            <h2 class="text-2xl font-bold text-green-600 mt-2">
                Rp{{ number_format($totalLunas, 0, ',', '.') }}
            </h2>
            <p class="text-xs text-slate-400 mt-1">Pembayaran tercatat</p>
        </div>
    </div>

    {{-- DAFTAR DENDA --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Daftar Keterlambatan dan Denda</h2>
            <p class="text-sm text-slate-500 mt-1">
                Denda sebesar Rp10.000 per hari, mulai dihitung satu hari setelah jatuh tempo.
            </p>
        </div>

        @forelse($peminjamans as $peminjaman)
            @php
                $denda = $peminjaman->status === 'dikembalikan'
                    ? (int) $peminjaman->total_denda
                    : $peminjaman->hitungDenda();

                $hariTerlambat = $peminjaman->hariTerlambatUntukDenda();
                $sudahLunas = $peminjaman->status_denda === 'lunas';
            @endphp

            <div class="p-5 border-b border-slate-100 last:border-b-0">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">

                    <div class="flex-1 min-w-0 space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-bold text-slate-800">
                                {{ $peminjaman->kode }}
                            </span>

                            @if($peminjaman->status === 'dipinjam')
                                <span class="rounded-full bg-orange-100 text-orange-700 px-3 py-1 text-xs font-semibold">
                                    Terlambat
                                </span>
                            @else
                                <span class="rounded-full bg-blue-100 text-blue-700 px-3 py-1 text-xs font-semibold">
                                    Sudah Dikembalikan
                                </span>
                            @endif

                            @if($sudahLunas)
                                <span class="rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-semibold">
                                    Lunas
                                </span>
                            @elseif($denda > 0)
                                <span class="rounded-full bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold">
                                    Belum Lunas
                                </span>
                            @endif
                        </div>

                        <div>
                            <p class="font-semibold text-slate-700">
                                {{ $peminjaman->user->name ?? 'Pengguna tidak ditemukan' }}
                            </p>
                            <p class="text-sm text-slate-500">
                                {{ $peminjaman->user->username ?? '-' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-500">
                            <span>
                                <i class="far fa-calendar mr-1"></i>
                                Jatuh tempo:
                                {{ $peminjaman->tanggal_jatuh_tempo?->format('d M Y') ?? '-' }}
                            </span>

                            <span>
                                <i class="far fa-clock mr-1"></i>
                                {{ $hariTerlambat }} hari terlambat
                            </span>
                        </div>

                        <div class="space-y-1">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Alat yang dipinjam
                            </p>

                            @foreach($peminjaman->items as $item)
                                <p class="text-sm text-slate-600">
                                    • {{ $item->alat->nama_alat ?? 'Nama alat tidak tersedia' }}
                                    ({{ $item->jumlah }} unit)
                                </p>
                            @endforeach
                        </div>

                        @if($sudahLunas)
                            <p class="text-xs text-green-700">
                                Dibayar pada:
                                {{ $peminjaman->denda_dibayar_pada?->format('d M Y, H:i') ?? '-' }}
                                · Petugas:
                                {{ $peminjaman->adminDenda->name ?? '-' }}
                            </p>
                        @endif
                    </div>

                    <div class="lg:w-56 lg:text-right space-y-3">
                        <div>
                            <p class="text-xs text-slate-400">Total Denda</p>
                            <p class="text-xl font-bold {{ $sudahLunas ? 'text-green-600' : 'text-red-600' }}">
                                Rp{{ number_format($denda, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-slate-400">
                                {{ $hariTerlambat }} hari × Rp10.000
                            </p>
                        </div>

                        @if($peminjaman->status === 'dikembalikan' && $denda > 0 && !$sudahLunas)
                            <form action="{{ route('denda.lunas', $peminjaman->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Catat denda ini sebagai lunas?')">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="w-full rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Tandai Lunas
                                </button>
                            </form>
                        @elseif($sudahLunas)
                            <div class="rounded-xl bg-green-50 px-4 py-2.5 text-sm font-semibold text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                Sudah Dibayar
                            </div>
                        @else
                            <div class="rounded-xl bg-orange-50 px-4 py-2.5 text-xs text-orange-700">
                                Menunggu pengembalian alat
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="p-10 text-center">
                <div class="text-4xl text-slate-300 mb-3">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3 class="font-semibold text-slate-700">
                    Belum Ada Data Denda
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    Data keterlambatan dan denda akan muncul di halaman ini.
                </p>
            </div>
        @endforelse

        @if($peminjamans->hasPages())
            <div class="p-5 border-t border-slate-100">
                {{ $peminjamans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection