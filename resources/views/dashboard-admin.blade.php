@extends('layouts.app')
@section('judul', 'Dashboard Petugas')

@section('konten')
<div class="space-y-8">

    {{-- HEADER --}}
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-800 via-blue-700 to-teal-700 p-6 text-white shadow-lg sm:p-8">
        <div class="relative z-10 flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-3 py-1 text-xs font-bold text-white">
                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                    SIPALA · Panel Petugas
                </span>

                <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                    Ringkasan Hari Ini
                </h1>

                <p class="mt-2 text-sm font-medium text-white sm:text-base">
                    Pantau persetujuan peminjaman dan ketersediaan alat sekolah.
                </p>

                <p class="mt-4 text-sm font-semibold text-white">
                    {{ now()->translatedFormat('l, j F Y') }}
                </p>
            </div>

            <div class="hidden h-24 w-24 shrink-0 items-center justify-center rounded-3xl border border-white/30 bg-white/15 text-white sm:flex">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-7 8h6m-6 4h6m-9-4h.01M9 17h.01"/>
                </svg>
            </div>
        </div>

        <div class="absolute -right-10 -top-12 h-44 w-44 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-16 right-32 h-36 w-36 rounded-full bg-white/10"></div>
    </header>

    {{-- STATISTIK --}}
    <section>
        <div class="mb-4">
            <h2 class="text-lg font-bold text-slate-900">Statistik Inventaris</h2>
            <p class="mt-1 text-sm font-medium text-slate-600">Ringkasan kondisi inventaris dan peminjaman.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- TOTAL ALAT --}}
            <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Jenis Alat</p>
                        <p class="angka mt-3 text-3xl font-extrabold text-slate-900">{{ $totalAlat }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-600">Jenis alat terdaftar</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- UNIT TERSEDIA --}}
            <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Unit Tersedia</p>
                        <p class="angka mt-3 text-3xl font-extrabold text-teal-800">{{ $unitTersedia }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-600">Siap dipinjam</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-100 text-teal-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 3c-2.755 0-5.285.93-7.298 2.49A12.03 12.03 0 003 12c0 5.25 3.375 9.71 8.062 11.333a2.5 2.5 0 001.876 0C17.625 21.71 21 17.25 21 12c0-2.08-.528-4.036-1.382-5.734z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- MENUNGGU PERSETUJUAN --}}
            <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Menunggu Persetujuan</p>
                        <p class="angka mt-3 text-3xl font-extrabold text-amber-800">{{ $menunggu }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-600">Perlu ditinjau petugas</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="9"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7v5l3 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- TERLAMBAT --}}
            <div class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Lewat Jatuh Tempo</p>
                        <p class="angka mt-3 text-3xl font-extrabold text-red-800">{{ $terlambat }}</p>
                        <p class="mt-2 text-xs font-medium text-slate-600">Perlu ditindaklanjuti</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-100 text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 4h.01M10.3 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.7 3.86a2 2 0 00-3.4 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ANTREAN PERSETUJUAN --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-200 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Antrean Persetujuan</h2>
                <p class="mt-1 text-sm font-medium text-slate-600">Pengajuan peminjaman yang perlu diproses.</p>
            </div>

            <a href="{{ route('persetujuan.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-100 px-4 py-2.5 text-sm font-bold text-blue-800 transition hover:bg-blue-200">
                Lihat Semua
                <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        <div class="space-y-3 p-4 sm:p-5">
            @forelse ($antrean as $p)
                <div class="rounded-xl border border-slate-200 p-4 transition hover:border-blue-300 hover:bg-slate-50 sm:p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="angka text-sm font-bold text-blue-800">{{ $p->kode }}</span>
                                <span class="text-xs font-medium text-slate-600">· {{ $p->created_at->diffForHumans() }}</span>
                            </div>

                            <p class="mt-2 font-bold text-slate-900">
                                {{ $p->user->name ?? 'Pengguna' }}
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-700">
                                Keperluan: {{ $p->keperluan ?: '-' }}
                            </p>

                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($p->items as $i)
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-800">
                                        {{ $i->alat->nama ?? 'Alat' }} × {{ $i->jumlah }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <a href="{{ route('peminjaman.show', $p) }}"
                           class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-800">
                            Proses Pengajuan
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 px-5 py-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-teal-100 text-teal-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="mt-3 font-bold text-slate-900">Tidak Ada Antrean</h3>
                    <p class="mt-1 text-sm font-medium text-slate-600">
                        Belum ada pengajuan yang menunggu persetujuan.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- STOK MENIPIS --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 p-5 sm:p-6">
            <h2 class="text-lg font-bold text-slate-900">Stok Alat Menipis</h2>
            <p class="mt-1 text-sm font-medium text-slate-600">Pantau alat yang jumlah tersedianya sedikit.</p>
        </div>

        @if (count($stokMenipis))
            <div class="overflow-x-auto">
                <table class="w-full min-w-[480px] text-left text-sm">
                    <thead class="bg-slate-100 text-xs uppercase tracking-wide text-slate-700">
                        <tr>
                            <th class="px-5 py-4 font-bold">Kode Alat</th>
                            <th class="px-5 py-4 font-bold">Nama Alat</th>
                            <th class="px-5 py-4 text-right font-bold">Stok Tersedia</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach ($stokMenipis as $alat)
                            <tr class="transition hover:bg-slate-50">
                                <td class="angka px-5 py-4 font-semibold text-slate-700">
                                    {{ $alat->kode }}
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-900">
                                    {{ $alat->nama }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <span class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-bold
                                        {{ $alat->stok_tersedia === 0
                                            ? 'bg-red-100 text-red-800'
                                            : ($alat->stok_tersedia <= 3
                                                ? 'bg-amber-100 text-amber-800'
                                                : 'bg-teal-100 text-teal-800') }}">
                                        {{ $alat->stok_tersedia }} / {{ $alat->stok_total }} unit
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center">
                <p class="font-bold text-slate-900">Stok aman</p>
                <p class="mt-1 text-sm font-medium text-slate-600">Tidak ada alat yang masuk daftar stok menipis.</p>
            </div>
        @endif
    </section>

</div>
@endsection
