@extends('layouts.app')
@section('judul', $alat->nama)

@section('konten')
<div class="space-y-6">

    {{-- Header halaman --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('alat.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-blue-200 transition hover:text-white">
                <span>←</span> Kembali ke katalog
            </a>

            <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-white">
                Detail Alat
            </h1>
            <p class="mt-2 text-sm text-blue-100/80">
                Informasi lengkap alat yang tersedia di SIPALA.
            </p>
        </div>

        <div class="rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-semibold text-white">
            Kode Alat:
            <span class="ml-1 text-cyan-200">{{ $alat->kode }}</span>
        </div>
    </div>

    {{-- Kartu detail alat --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

        {{-- Bagian nama alat --}}
        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5 sm:px-8">
            <p class="text-sm font-semibold uppercase tracking-wider text-blue-700">
                Informasi Alat
            </p>

            <h2 class="mt-2 text-2xl font-extrabold leading-tight text-slate-900 sm:text-3xl">
                {{ $alat->nama }}
            </h2>

            <p class="mt-2 text-sm text-slate-600">
                Kode inventaris: {{ $alat->kode }}
            </p>
        </div>

        <div class="p-6 sm:p-8">

            {{-- Foto alat --}}
            @if ($alat->foto)
                <div class="mb-8 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                    <img
                        src="{{ Storage::url($alat->foto) }}"
                        alt="{{ $alat->nama }}"
                        class="max-h-[420px] w-full object-contain"
                    >
                </div>
            @endif

            {{-- Informasi alat --}}
            <h3 class="mb-4 text-lg font-bold text-slate-900">
                Informasi dan Ketersediaan
            </h3>

            <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

                @foreach ([
                    'Kategori' => $alat->kategori?->nama ?? 'Belum diisi',
                    'Merk' => $alat->merk ?: 'Belum diisi',
                    'Lokasi Simpan' => $alat->lokasi ?: 'Belum diisi',
                    'Kondisi' => \App\Models\Alat::KONDISI[$alat->kondisi] ?? ucfirst($alat->kondisi),
                    'Stok Total' => $alat->stok_total . ' unit',
                    'Tersedia Sekarang' => $alat->stok_tersedia . ' unit',
                ] as $label => $nilai)

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <dt class="text-sm font-medium text-slate-600">
                            {{ $label }}
                        </dt>

                        <dd class="mt-2 break-words text-base font-bold text-slate-900">
                            {{ $nilai }}
                        </dd>
                    </div>

                @endforeach

            </dl>

            {{-- Deskripsi --}}
            @if ($alat->deskripsi)
                <div class="mt-8">
                    <h3 class="mb-3 text-lg font-bold text-slate-900">
                        Deskripsi Alat
                    </h3>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <p class="whitespace-pre-line text-sm leading-7 text-slate-700">
                            {{ $alat->deskripsi }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Tombol aksi --}}
            <div class="mt-8 flex flex-wrap gap-3 border-t border-slate-200 pt-6">

                @if ($alat->stok_tersedia > 0)
                    <a href="{{ route('peminjaman.create', ['alat' => $alat->id]) }}"
                       class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-5 py-3 text-sm font-bold text-white shadow-md transition hover:bg-blue-800">
                        Ajukan Peminjaman
                    </a>
                @else
                    <p class="w-full rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800">
                        Stok sedang tidak tersedia. Silakan cek kembali setelah ada pengembalian.
                    </p>
                @endif

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('alat.edit', $alat) }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 transition hover:bg-slate-100">
                        Ubah Data
                    </a>

                    <form method="POST"
                          action="{{ route('alat.destroy', $alat) }}"
                          onsubmit="return confirm('Hapus {{ $alat->nama }} dari katalog?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-bold text-red-700 transition hover:bg-red-100">
                            Hapus Alat
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
