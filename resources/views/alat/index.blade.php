@extends('layouts.app')
@section('judul', 'Katalog Alat')

@section('konten')

<div class="w-full min-w-0 space-y-5">

    {{-- HEADER --}}
    <header class="relative flex flex-col gap-4 overflow-hidden rounded-2xl bg-gradient-to-r from-blue-700 via-blue-600 to-teal-500 p-5 text-white shadow-lg sm:flex-row sm:items-end sm:justify-between sm:rounded-3xl sm:p-7">

        <div class="pointer-events-none absolute -right-5 -top-10 h-36 w-36 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute right-32 top-20 h-24 w-24 rounded-full bg-white/10"></div>

        <div class="relative z-10">
            <h1 class="text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                Katalog Alat
            </h1>

            <p class="mt-1 text-xs text-blue-100 sm:text-sm">
                {{ $alats->total() }} jenis alat terdaftar.
            </p>
        </div>

        @if (auth()->user()->isAdmin())
            <a href="{{ route('alat.create') }}"
               class="relative z-10 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-white bg-white px-4 py-3 text-sm font-bold text-blue-700 shadow-lg transition hover:bg-blue-50 sm:w-auto sm:px-5">
                <span class="text-lg">＋</span>
                Tambah Alat
            </a>
        @endif

    </header>

    {{-- PENCARIAN DAN FILTER --}}
    <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-12">

        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari nama, kode, atau merk alat..."
            class="min-w-0 w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 sm:col-span-2 xl:col-span-5"
        >

        <select
            name="kategori"
            class="min-w-0 w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 xl:col-span-3">

            <option value="">Semua Kategori</option>

            @foreach ($kategoris as $kategori)
                <option value="{{ $kategori->id }}"
                    @selected(request('kategori') == $kategori->id)>
                    {{ $kategori->nama }}
                </option>
            @endforeach

        </select>

        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white px-3 py-3 text-sm font-medium text-slate-700 shadow-sm xl:col-span-2">
            <input
                type="checkbox"
                name="tersedia"
                value="1"
                @checked(request()->boolean('tersedia'))
                class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
            >
            <span class="whitespace-nowrap">Tersedia saja</span>
        </label>

        <button
            type="submit"
            class="w-full rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-md transition hover:bg-blue-700 sm:col-span-2 xl:col-span-2">
            Cari
        </button>

    </form>

    {{-- KARTU ALAT --}}
    <div class="grid min-w-0 grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

        @forelse ($alats as $alat)

            <article class="group flex min-w-0 flex-col overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:border-blue-300 hover:shadow-xl">

                {{-- FOTO ALAT --}}
                <div class="relative flex h-40 items-center justify-center overflow-hidden bg-slate-50 p-3">

                    @if ($alat->foto)
                        <img
                            src="{{ asset('storage/' . $alat->foto) }}"
                            alt="{{ $alat->nama }}"
                            class="h-full w-full object-contain transition duration-500 group-hover:scale-105"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >

                        <div class="hidden h-full w-full items-center justify-center text-4xl">
                            🧰
                        </div>
                    @else
                        <div class="flex h-full w-full items-center justify-center text-4xl">
                            🧰
                        </div>
                    @endif

                    {{-- STATUS --}}
                    <span class="absolute right-3 top-3 rounded-full px-2.5 py-1 text-xs font-bold shadow-sm
                        {{ $alat->stok_tersedia > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ $alat->stok_tersedia > 0 ? '● Tersedia' : '● Habis' }}
                    </span>

                </div>

                {{-- INFORMASI ALAT --}}
                <div class="flex flex-1 flex-col p-4">

                    <div class="mb-2">
                        <span class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold tracking-wide text-blue-700">
                            {{ $alat->kode }}
                        </span>
                    </div>

                    <h2 class="break-words text-base font-extrabold leading-snug text-slate-900">
                        {{ $alat->nama }}
                    </h2>

                    <p class="mt-1 break-words text-xs text-slate-500">
                        {{ $alat->kategori?->nama ?? 'Tanpa kategori' }}

                        @if ($alat->merk)
                            · {{ $alat->merk }}
                        @endif
                    </p>

                    {{-- STOK --}}
                    <div class="mt-4 rounded-xl bg-gradient-to-r from-teal-50 to-emerald-50 px-3 py-2">
                        <p class="text-xs font-medium text-slate-500">
                            Stok alat tersedia
                        </p>

                        <p class="mt-1 text-sm font-extrabold
                            {{ $alat->stok_tersedia > 0 ? 'text-teal-700' : 'text-rose-600' }}">
                            {{ $alat->stok_tersedia }} dari {{ $alat->stok_total }} unit tersedia
                        </p>
                    </div>

                    {{-- TOMBOL --}}
                    <div class="mt-auto grid grid-cols-2 gap-2 pt-4">

                        <a href="{{ route('alat.show', $alat) }}"
                           class="rounded-lg border border-blue-200 bg-white px-3 py-2.5 text-center text-xs font-bold text-blue-700 transition hover:bg-blue-50">
                            Detail
                        </a>

                        @if ($alat->stok_tersedia > 0)
                            <a href="{{ route('peminjaman.create', ['alat' => $alat->id]) }}"
                               class="rounded-lg bg-blue-600 px-3 py-2.5 text-center text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                                Pinjam
                            </a>
                        @else
                            <span class="rounded-lg bg-slate-100 px-3 py-2.5 text-center text-xs font-bold text-slate-400">
                                Stok Habis
                            </span>
                        @endif

                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('alat.edit', $alat) }}"
                               class="col-span-2 rounded-lg bg-amber-100 px-3 py-2.5 text-center text-xs font-bold text-amber-700 transition hover:bg-amber-200">
                                Ubah Alat
                            </a>
                        @endif

                    </div>

                </div>

            </article>

        @empty

            <div class="col-span-full rounded-2xl border-2 border-dashed border-blue-200 bg-white/80 p-8 text-center sm:p-10">
                <div class="mb-3 text-4xl">🔎</div>

                <p class="font-bold text-slate-800">
                    Alat tidak ditemukan
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Coba kata kunci atau kategori lainnya.
                </p>

                <a href="{{ route('alat.index') }}"
                   class="mt-4 inline-block rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-blue-700">
                    Reset Pencarian
                </a>
            </div>

        @endforelse

    </div>

    {{-- PAGINATION --}}
    <div class="overflow-x-auto">
        {{ $alats->links() }}
    </div>

</div>

@endsection