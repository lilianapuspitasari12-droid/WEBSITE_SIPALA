@extends('layouts.app')
@section('judul', 'Pengembalian Alat')

@section('konten')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-teal-500 p-6 text-white shadow-lg">
        <div class="relative z-10">
            <h1 class="mt-2 text-2xl font-bold md:text-3xl">Pengembalian Alat</h1>
            <p class="mt-2 max-w-2xl text-sm text-blue-50">
                Kelola pengembalian alat dan lengkapi bukti pengembalian sesuai kondisi alat.
            </p>
        </div>
        <div class="absolute -right-8 -top-12 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-16 right-24 h-36 w-36 rounded-full bg-white/10"></div>
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('sukses'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
            {{ session('sukses') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- DAFTAR PEMINJAMAN --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 p-5">
            <h2 class="text-lg font-bold text-slate-800">Daftar Peminjaman Aktif</h2>
            <p class="mt-1 text-sm text-slate-500">
                Pilih peminjaman untuk membuka detail dan mengajukan pengembalian.
            </p>
        </div>

        <div class="space-y-4 p-5">
            @forelse($peminjamans as $peminjaman)
                <div class="rounded-2xl border border-slate-200 p-4 transition hover:border-blue-300 hover:shadow-sm">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">
                                Kode Peminjaman
                            </p>

                            <h3 class="mt-1 break-words text-lg font-bold text-slate-800">
                                {{ $peminjaman->kode }}
                            </h3>

                            <p class="mt-2 text-sm text-slate-600">
                                Peminjam:
                                <span class="font-medium text-slate-800">
                                    {{ $peminjaman->user->name ?? 'Pengguna' }}
                                </span>
                            </p>

                            <p class="mt-1 text-sm text-slate-600">
                                Tanggal pinjam:
                                {{ $peminjaman->tanggal_pinjam
                                    ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y')
                                    : '-' }}
                            </p>

                            <span class="mt-3 inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                {{ $peminjaman->statusLabel() }}
                            </span>
                        </div>

                        <div class="shrink-0">
                            <a href="{{ route('pengembalian.show', $peminjaman) }}"
                               class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 sm:w-auto">
                                Detail Pengembalian
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 px-5 py-12 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-2xl">
                        📦
                    </div>
                    <h3 class="mt-4 font-bold text-slate-800">Belum Ada Peminjaman Aktif</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Peminjaman yang sedang dipinjam akan muncul di halaman ini.
                    </p>
                </div>
            @endforelse
        </div>

        @if(method_exists($peminjamans, 'links'))
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $peminjamans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
