@extends('layouts.app')
@section('judul', 'Persetujuan')

@section('konten')
    <div class="space-y-6">

{{-- HEADER --}}
<div class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-teal-500 p-8 text-white shadow-lg">

    {{-- Hiasan lingkaran --}}
    <div class="pointer-events-none absolute -right-5 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
    <div class="pointer-events-none absolute right-32 top-20 h-24 w-24 rounded-full bg-white/10"></div>

    <div class="relative z-10">
        <h1 class="mb-1 text-3xl font-extrabold tracking-tight text-white">
            Persetujuan
        </h1>

        <p class="text-sm text-blue-100">
            Proses pengajuan berdasarkan urutan masuk.
        </p>
    </div>

</div>

        {{-- FILTER STATUS --}}
        <nav class="flex flex-wrap gap-2">
            @foreach ([
                'menunggu' => 'Menunggu',
                'disetujui' => 'Siap diserahkan',
                'dipinjam' => 'Sedang dipinjam'
            ] as $nilai => $label)
                <a href="{{ route('persetujuan.index', ['status' => $nilai]) }}"
                   class="rounded-lg border px-3 py-2 text-sm font-bold transition
                   {{ $statusAktif === $nilai
                       ? 'border-blue-800 bg-blue-800 text-white'
                       : 'border-slate-300 bg-white text-slate-800 hover:border-blue-500 hover:bg-blue-50' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        {{-- DAFTAR PENGAJUAN --}}
        @forelse ($peminjamans as $p)
            <div class="mb-3 rounded-xl border border-slate-300 bg-white p-5 shadow-sm transition hover:shadow-md">
                <div class="flex flex-wrap items-start justify-between gap-4">

                    <div class="min-w-0 flex-1">
                        <p class="angka text-sm font-semibold text-slate-700">
                            {{ $p->kode }} · diajukan {{ $p->created_at->diffForHumans() }}
                        </p>

                        <p class="mt-2 text-lg font-extrabold text-slate-900">
                            {{ $p->user->name ?? 'Pengguna' }}
                        </p>

                        <p class="mt-1 font-medium text-slate-700">
                            {{ $p->keperluan ?: '-' }}
                        </p>

                        <ul class="mt-3 space-y-1 text-sm font-medium text-slate-700">
                            @foreach ($p->items as $item)
                                <li>
                                    {{ $item->alat->nama ?? 'Alat' }} —
                                    {{ $item->jumlah }} unit
                                    (sisa stok {{ $item->alat->stok_tersedia ?? 0 }})
                                </li>
                            @endforeach
                        </ul>

                        <p class="angka mt-3 text-sm font-bold text-slate-800">
                            {{ $p->tanggal_pinjam->translatedFormat('j M') }}
                            –
                            {{ $p->tanggal_jatuh_tempo->translatedFormat('j M Y') }}
                        </p>

                        {{-- STATUS --}}
                        <div class="mt-3">
                            @if ($p->status === 'menunggu')
                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-900">
                                    Menunggu Persetujuan
                                </span>
                            @elseif ($p->status === 'disetujui')
                                <span class="inline-flex rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-900">
                                    Siap Diserahkan
                                </span>
                            @elseif ($p->status === 'dipinjam')
                                <span class="inline-flex rounded-full bg-teal-100 px-3 py-1 text-xs font-bold text-teal-900">
                                    Sedang Dipinjam
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="flex flex-col items-start gap-2 sm:items-end">
                        @if ($p->status === 'menunggu')
                            <form method="POST" action="{{ route('persetujuan.setujui', $p) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="rounded-lg bg-teal-700 px-4 py-2 font-bold text-white transition hover:bg-teal-800">
                                    Setujui
                                </button>
                            </form>

                        @elseif ($p->status === 'disetujui')
                            <form method="POST" action="{{ route('persetujuan.serahkan', $p) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="rounded-lg bg-blue-800 px-4 py-2 font-bold text-white transition hover:bg-blue-900">
                                    Alat Diserahkan
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('peminjaman.show', $p) }}"
                           class="text-sm font-bold text-blue-800 underline-offset-4 hover:text-blue-950 hover:underline">
                            Buka Detail
                        </a>
                    </div>

                </div>
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
                <p class="font-semibold text-slate-800">
                    Tidak ada pengajuan pada status ini.
                </p>
            </div>
        @endforelse

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $peminjamans->links() }}
        </div>

    </div>
@endsection
