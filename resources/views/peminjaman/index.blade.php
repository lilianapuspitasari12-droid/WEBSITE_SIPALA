
@extends('layouts.app')
@section('judul', 'Peminjaman')

@section('konten')
<div class="space-y-6">

    {{-- HEADER --}}
    <header class="overflow-hidden rounded-2xl bg-gradient-to-r from-blue-700 via-blue-600 to-teal-600 p-6 text-white shadow-lg sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white sm:text-3xl">
                    {{ auth()->user()->isAdmin() ? 'Data Peminjaman Alat' : 'Peminjaman Saya' }}
                </h1>
                <p class="mt-2 text-sm text-blue-50">
                    Pantau pengajuan dan status peminjaman alat praktik sekolah.
                </p>
            </div>

            <a href="{{ route('peminjaman.create') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-bold text-blue-700 shadow-md transition hover:-translate-y-0.5 hover:bg-blue-50">
                <span class="text-lg">＋</span>
                Ajukan Peminjaman
            </a>
        </div>
    </header>

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-700">Total Pengajuan</p>
                <span class="rounded-xl bg-blue-100 p-3 text-xl text-blue-700">▤</span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-blue-700">{{ $peminjamans->total() }}</p>
            <p class="mt-1 text-xs text-slate-500">Data sesuai filter yang dipilih</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-700">Menunggu</p>
                <span class="rounded-xl bg-amber-100 p-3 text-xl text-amber-700">◷</span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-amber-700">
                {{ \App\Models\Peminjaman::when(!auth()->user()->isAdmin(), fn ($q) => $q->milik(auth()->user()))->where('status', 'menunggu')->count() }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Menunggu persetujuan petugas</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-700">Sedang Dipinjam</p>
                <span class="rounded-xl bg-teal-100 p-3 text-xl text-teal-700">✓</span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-teal-700">
                {{ \App\Models\Peminjaman::when(!auth()->user()->isAdmin(), fn ($q) => $q->milik(auth()->user()))->where('status', 'dipinjam')->count() }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Alat masih digunakan</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10">
            <div class="flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-700">Dikembalikan</p>
                <span class="rounded-xl bg-emerald-100 p-3 text-xl text-emerald-700">↩</span>
            </div>
            <p class="mt-3 text-3xl font-extrabold text-emerald-700">
                {{ \App\Models\Peminjaman::when(!auth()->user()->isAdmin(), fn ($q) => $q->milik(auth()->user()))->where('status', 'dikembalikan')->count() }}
            </p>
            <p class="mt-1 text-xs text-slate-500">Peminjaman selesai</p>
        </div>

    </div>

    {{-- FILTER STATUS --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10">
        <div class="mb-4">
            <h2 class="font-bold text-slate-900">Filter Peminjaman</h2>
            <p class="mt-1 text-sm text-slate-600">Pilih status untuk mencari transaksi tertentu.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('peminjaman.index') }}"
               class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ !request('status') ? 'bg-blue-600 text-white shadow-md' : 'border border-slate-200 bg-slate-50 text-slate-700 hover:bg-blue-50 hover:text-blue-700' }}">
                Semua
            </a>

            @foreach (\App\Models\Peminjaman::STATUS as $nilai => $label)
                <a href="{{ route('peminjaman.index', ['status' => $nilai]) }}"
                   class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request('status') === $nilai ? 'bg-blue-600 text-white shadow-md' : 'border border-slate-200 bg-slate-50 text-slate-700 hover:bg-blue-50 hover:text-blue-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </section>

    {{-- TABEL --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-lg shadow-black/10">

        <div class="flex flex-wrap items-center justify-between gap-3 bg-gradient-to-r from-slate-50 to-blue-50 px-5 py-4">
            <div>
                <h2 class="font-bold text-slate-900">Daftar Transaksi Peminjaman</h2>
                <p class="mt-1 text-xs text-slate-600">Klik Detail untuk melihat informasi lengkap.</p>
            </div>
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">
                {{ $peminjamans->total() }} transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-slate-800">
                <thead class="bg-slate-100 text-xs uppercase tracking-wide text-slate-700">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Kode</th>
                        @if (auth()->user()->isAdmin())
                            <th class="px-5 py-4 font-semibold">Peminjam</th>
                        @endif
                        <th class="px-5 py-4 font-semibold">Alat</th>
                        <th class="px-5 py-4 font-semibold">Jatuh Tempo</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($peminjamans as $p)
                        <tr class="transition hover:bg-blue-50">
                            <td class="px-5 py-4">
                                <span class="font-bold text-blue-700">{{ $p->kode }}</span>
                                <span class="mt-1 block text-xs text-slate-500">
                                    {{ $p->tanggal_pinjam->translatedFormat('d M Y') }}
                                </span>
                            </td>

                            @if (auth()->user()->isAdmin())
                                <td class="px-5 py-4">
                                    <span class="block font-semibold text-slate-800">{{ $p->user->name }}</span>
                                    <span class="mt-1 block text-xs text-slate-600">{{ $p->user->kelas_jabatan }}</span>
                                </td>
                            @endif

                            <td class="max-w-xs px-5 py-4 text-sm text-slate-700">
                                {{ $p->items->map(fn ($i) => $i->alat->nama.' × '.$i->jumlah)->join(', ') }}
                            </td>

                            <td class="px-5 py-4 text-sm {{ $p->isTerlambat() ? 'font-bold text-red-600' : 'text-slate-700' }}">
                                {{ $p->tanggal_jatuh_tempo->translatedFormat('d M Y') }}
                                @if ($p->isTerlambat())
                                    <span class="mt-1 block text-xs">Terlambat</span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                @include('komponen-status', ['status' => $p->status])
                            </td>

                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('peminjaman.show', $p) }}"
                                   class="inline-block rounded-lg bg-blue-100 px-3 py-2 text-xs font-bold text-blue-800 transition hover:bg-blue-600 hover:text-white">
                                    Detail →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="px-5 py-14 text-center">
                                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-2xl text-blue-600">▤</div>
                                <p class="font-bold text-slate-800">Belum ada transaksi</p>
                                <p class="mt-1 text-sm text-slate-600">Coba pilih filter lain atau ajukan peminjaman alat.</p>
                                <a href="{{ route('peminjaman.create') }}"
                                   class="mt-4 inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    ＋ Ajukan Peminjaman
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-5 py-4 text-slate-800">
            {{ $peminjamans->links() }}
        </div>
    </section>

</div>
@endsection
