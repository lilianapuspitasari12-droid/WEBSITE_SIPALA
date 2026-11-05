
@extends('layouts.app')

@section('judul', 'Kelola Denda')

@section('konten')
<div class="space-y-6">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="relative z-10">
            <p class="!text-teal-300 text-sm font-bold mb-2">
                SIPALA / ADMIN
            </p>

            <h1 class="!text-white text-3xl font-extrabold mb-2">
                Kelola Denda
            </h1>

            <p class="!text-white text-sm font-medium">
                Pantau keterlambatan dan catat pembayaran denda peminjaman alat.
            </p>
        </div>
    </div>


    {{-- ================= NOTIFIKASI ================= --}}
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


    {{-- ================= PERHITUNGAN RINGKASAN ================= --}}
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


    {{-- ================= KARTU RINGKASAN ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Peminjaman Terlambat --}}
        <div class="rounded-2xl bg-white border border-slate-100 p-5 shadow-sm">
            <p class="text-sm text-black">
                Peminjaman Terlambat
            </p>

            <h2 class="text-2xl font-bold text-orange-600 mt-2">
                {{ $jumlahTerlambat }}
            </h2>

            <p class="text-xs text-black mt-1">
                Belum dikembalikan
            </p>
        </div>

        {{-- Denda Belum Lunas --}}
        <div class="rounded-2xl bg-white border border-slate-100 p-5 shadow-sm">
            <p class="text-sm text-black">
                Total Denda Belum Lunas
            </p>

            <h2 class="text-2xl font-bold text-red-600 mt-2">
                Rp{{ number_format($totalBelumLunas, 0, ',', '.') }}
            </h2>

            <p class="text-xs text-black mt-1">
                Termasuk denda berjalan
            </p>
        </div>

        {{-- Denda Lunas --}}
        <div class="rounded-2xl bg-white border border-slate-100 p-5 shadow-sm">
            <p class="text-sm text-black">
                Total Denda Lunas
            </p>

            <h2 class="text-2xl font-bold text-green-600 mt-2">
                Rp{{ number_format($totalLunas, 0, ',', '.') }}
            </h2>

            <p class="text-xs text-black mt-1">
                Pembayaran tercatat
            </p>
        </div>

    </div>


    {{-- ================= DAFTAR DENDA ================= --}}
    <div class="rounded-2xl border border-slate-100 bg-white shadow-sm overflow-hidden">

        {{-- Judul Daftar --}}
        <div class="p-5 border-b border-slate-100">
            <h2 class="font-bold text-black">
                Daftar Keterlambatan dan Denda
            </h2>

            <p class="text-sm text-black mt-1">
                Denda sebesar Rp10.000 per hari, mulai dihitung satu hari setelah jatuh tempo.
            </p>
        </div>


        {{-- Data Peminjaman --}}
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

                    {{-- Informasi Peminjaman --}}
                    <div class="flex-1 min-w-0 space-y-3">

                        {{-- Kode dan Status --}}
                        <div class="flex flex-wrap items-center gap-2">

                            <span class="font-bold text-black">
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


                        {{-- Informasi Pengguna --}}
                        <div>
                            <p class="font-semibold text-black">
                                {{ $peminjaman->user->name ?? 'Pengguna tidak ditemukan' }}
                            </p>

                            <p class="text-sm text-black">
                                {{ $peminjaman->user->username ?? '-' }}
                            </p>
                        </div>


                        {{-- Informasi Tanggal --}}
                        <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-black">

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


                        {{-- Daftar Alat --}}
                        <div class="space-y-1">

                            <p class="text-xs font-semibold uppercase tracking-wide text-black">
                                Alat yang dipinjam
                            </p>

                            @foreach($peminjaman->items as $item)
                                <p class="text-sm text-black">
                                    • {{ $item->alat->nama_alat ?? 'Nama alat tidak tersedia' }}
                                    ({{ $item->jumlah }} unit)
                                </p>
                            @endforeach

                        </div>


                        {{-- Informasi Pembayaran --}}
                        @if($sudahLunas)
                            <p class="text-xs text-green-700">
                                Dibayar pada:
                                {{ $peminjaman->denda_dibayar_pada?->format('d M Y, H:i') ?? '-' }}

                                · Petugas:
                                {{ $peminjaman->adminDenda->name ?? '-' }}
                            </p>
                        @endif

                    </div>


                    {{-- Informasi dan Aksi Denda --}}
                    <div class="lg:w-56 lg:text-right space-y-3">

                        {{-- Total Denda --}}
                        <div>
                            <p class="text-xs text-black">
                                Total Denda
                            </p>

                            <p class="text-xl font-bold {{ $sudahLunas ? 'text-green-600' : 'text-red-600' }}">
                                Rp{{ number_format($denda, 0, ',', '.') }}
                            </p>

                            <p class="text-xs text-black">
                                {{ $hariTerlambat }} hari × Rp10.000
                            </p>
                        </div>


                        {{-- Tombol Tandai Lunas --}}
                        @if($peminjaman->status === 'dikembalikan' && $denda > 0 && !$sudahLunas)

                            <form
                                action="{{ route('denda.lunas', $peminjaman->id) }}"
                                method="POST"
                                onsubmit="return confirm('Catat denda ini sebagai lunas?')"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="w-full rounded-xl bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700 transition"
                                >
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Tandai Lunas
                                </button>
                            </form>

                        {{-- Status Sudah Dibayar --}}
                        @elseif($sudahLunas)

                            <div class="rounded-xl bg-green-50 px-4 py-2.5 text-sm font-semibold text-green-700">
                                <i class="fas fa-check-circle mr-1"></i>
                                Sudah Dibayar
                            </div>

                        {{-- Menunggu Pengembalian --}}
                        @else

                            <div class="rounded-xl bg-orange-50 px-4 py-2.5 text-xs text-orange-700">
                                Menunggu pengembalian alat
                            </div>

                        @endif

                    </div>

                </div>
            </div>

        @empty

            {{-- Tampilan Jika Belum Ada Data --}}
            <div class="p-10 text-center">

                <div class="text-4xl text-black mb-3">
                    <i class="fas fa-receipt"></i>
                </div>

                <h3 class="font-semibold text-black">
                    Belum Ada Data Denda
                </h3>

                <p class="text-sm text-black mt-1">
                    Data keterlambatan dan denda akan muncul di halaman ini.
                </p>

            </div>

        @endforelse


        {{-- Pagination --}}
        @if($peminjamans->hasPages())
            <div class="p-5 border-t border-slate-100">
                {{ $peminjamans->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
