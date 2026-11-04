@extends('layouts.app')
@section('judul', 'Riwayat Peminjaman')

@section('konten')
<div class="space-y-6">

    {{-- HEADER --}}
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-teal-500 p-6 text-white shadow-xl sm:p-8">
        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute bottom-0 right-32 h-24 w-24 rounded-full bg-cyan-300/20"></div>

        <div class="relative">
            <h1 class="text-2xl font-extrabold sm:text-3xl">Riwayat Peminjaman</h1>
            <p class="mt-2 max-w-xl text-sm leading-6 text-blue-50">
                Lihat catatan peminjaman, status Ppeminjaman, dan detail alat dalam satu halaman.
            </p>
        </div>
    </header>

   
{{-- STATISTIK --}}
<section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

    {{-- TOTAL PEMINJAMAN --}}
    <div class="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-sky-100 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-blue-800">Total Peminjaman</p>
                <p class="mt-2 text-3xl font-extrabold text-blue-700">{{ $total }}</p>
                <p class="mt-1 text-xs text-blue-700">Seluruh catatan peminjaman</p>
            </div>

            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/80 text-blue-600 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 5h8M8 9h8M8 13h5m-9 7h14a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v13a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- DISETUJUI --}}
    <div class="rounded-2xl border border-teal-100 bg-gradient-to-br from-teal-50 to-cyan-100 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-teal-800">Disetujui atau Dipinjam</p>
                <p class="mt-2 text-3xl font-extrabold text-teal-700">{{ $disetujui }}</p>
                <p class="mt-1 text-xs text-teal-700">Peminjaman yang masih berjalan</p>
            </div>

            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/80 text-teal-600 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l4 4L19 6"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a9 9 0 109 9"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- SELESAI --}}
    <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 to-green-100 p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-emerald-800">Selesai Dikembalikan</p>
                <p class="mt-2 text-3xl font-extrabold text-emerald-700">{{ $selesai }}</p>
                <p class="mt-1 text-xs text-emerald-700">Peminjaman yang sudah selesai</p>
            </div>

            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/80 text-emerald-600 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 22a10 10 0 110-20 10 10 0 010 20z"/>
                </svg>
            </div>
        </div>
    </div>

</section>


{{-- PENCARIAN --}}
<section class="rounded-2xl border border-blue-100 bg-white shadow-md shadow-blue-100/40">
    <div class="border-b border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50 px-5 py-4 sm:px-6">
        <h2 class="text-lg font-extrabold text-black">Cari Peminjaman</h2>
        <p class="mt-1 text-sm text-black">Gunakan kata kunci dan status untuk menemukan Peminjaman.</p>
    </div>

    <form action="{{ route('riwayat.index') }}" method="GET"
          class="flex flex-col gap-3 p-5 sm:flex-row sm:flex-wrap sm:items-center sm:px-6">

        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari kode atau nama alat..."
            class="min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-black outline-none transition placeholder:text-gray-600 focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
        >

        <select
            name="status"
            class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-black outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
        >
            <option value="">Semua Status</option>
            <option value="menunggu" @selected(request('status') === 'menunggu')>Menunggu</option>
            <option value="disetujui" @selected(request('status') === 'disetujui')>Disetujui</option>
            <option value="dipinjam" @selected(request('status') === 'dipinjam')>Dipinjam</option>
            <option value="ditolak" @selected(request('status') === 'ditolak')>Ditolak</option>
            <option value="dikembalikan" @selected(request('status') === 'dikembalikan')>Dikembalikan</option>
            <option value="dibatalkan" @selected(request('status') === 'dibatalkan')>Dibatalkan</option>
        </select>

        <button type="submit"
                class="rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-3 text-sm font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            Cari Peminjaman
        </button>

        <a href="{{ route('riwayat.index') }}"
           class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-center text-sm font-bold text-black transition hover:border-blue-300 hover:bg-blue-50">
            Reset
        </a>
    </form>
</section>

    {{-- TABEL --}}
    <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-lg shadow-blue-100/50">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-blue-100 bg-gradient-to-r from-blue-50 to-cyan-50 px-5 py-5 sm:px-6">
            <div>
                <h2 class="text-lg font-extrabold text-slate-800">Daftar Transaksi</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Menampilkan {{ $peminjamans->count() }} data pada halaman ini.
                </p>
            </div>
            <span class="rounded-xl bg-blue-100 px-3 py-2 text-xs font-bold text-blue-700">
                {{ $peminjamans->total() }} total data
            </span>
        </div>

        @if($peminjamans->isEmpty())
            <div class="px-5 py-14 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-gradient-to-br from-blue-100 to-teal-100 text-3xl text-blue-700">
                    ⌕
                </div>
                <h3 class="mt-4 text-lg font-bold text-slate-800">Transaksi Tidak Ditemukan</h3>
                <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                    Coba kata kunci lain atau ubah filter status yang kamu pilih.
                </p>
                <a href="{{ route('riwayat.index') }}"
                   class="mt-5 inline-flex rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
                    Tampilkan Semua
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-4 font-bold">Kode Peminjaman</th>
                            @if(auth()->user()->isAdmin())
                                <th class="px-5 py-4 font-bold">Peminjam</th>
                            @endif
                            <th class="px-5 py-4 font-bold">Daftar Alat</th>
                            <th class="px-5 py-4 font-bold">Status</th>
                            <th class="px-5 py-4 text-center font-bold">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach($peminjamans as $peminjaman)
                            <tr class="transition hover:bg-blue-50/70">
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-lg bg-blue-50 px-3 py-2 font-bold text-blue-700">
                                        {{ $peminjaman->kode }}
                                    </span>
                                </td>

                                @if(auth()->user()->isAdmin())
                                    <td class="px-5 py-4 font-semibold text-slate-700">
                                        {{ $peminjaman->user->name ?? '-' }}
                                    </td>
                                @endif

                                <td class="px-5 py-4 text-slate-600">
                                    @forelse($peminjaman->items as $item)
                                        <div class="mb-1 flex items-center gap-2">
                                            <span class="text-teal-500">●</span>
                                            <span>{{ $item->alat->nama ?? 'Alat tidak tersedia' }}</span>
                                        </div>
                                    @empty
                                        <span class="text-slate-400">Tidak ada rincian alat</span>
                                    @endforelse
                                </td>

                                <td class="px-5 py-4">
                                    @php
                                        $warnaStatus = match ($peminjaman->status) {
                                            'dikembalikan' => 'bg-emerald-100 text-emerald-800',
                                            'dipinjam' => 'bg-amber-100 text-amber-800',
                                            'disetujui' => 'bg-blue-100 text-blue-800',
                                            'menunggu' => 'bg-orange-100 text-orange-800',
                                            'ditolak' => 'bg-red-100 text-red-800',
                                            'dibatalkan' => 'bg-slate-200 text-slate-700',
                                            default => 'bg-slate-100 text-slate-700',
                                        };
                                    @endphp

                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold {{ $warnaStatus }}">
                                        <span class="h-2 w-2 rounded-full bg-current"></span>
                                        {{ $peminjaman->statusLabel() }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('peminjaman.show', $peminjaman) }}"
                                       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-600 px-4 py-2.5 font-bold text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                        Detail →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 bg-slate-50 px-5 py-4 sm:px-6">
                {{ $peminjamans->withQueryString()->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
