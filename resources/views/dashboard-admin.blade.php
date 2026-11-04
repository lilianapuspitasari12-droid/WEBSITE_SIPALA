@extends('layouts.app')
@section('judul', 'Dashboard Petugas')

@section('konten')

@php
    $notifikasiDenda = \App\Models\Peminjaman::with(['user', 'items.alat'])
        ->where(function ($query) {
            $query->where(function ($q) {
                $q->where('status', 'dipinjam')
                    ->whereDate('tanggal_jatuh_tempo', '<', today());
            })->orWhere(function ($q) {
                $q->where('status', 'dikembalikan')
                    ->where('total_denda', '>', 0)
                    ->where(function ($d) {
                        $d->whereNull('status_denda')
                          ->orWhere('status_denda', '!=', 'lunas');
                    });
            });
        })
        ->latest()
        ->get();

    $jumlahNotifikasi = $notifikasiDenda->count();

    $totalDendaNotifikasi = $notifikasiDenda->sum(function ($p) {
        return $p->status === 'dikembalikan'
            ? (int) $p->total_denda
            : (int) $p->hitungDenda();
    });
@endphp

<div class="space-y-8">

    {{-- HEADER --}}
    <header class="relative overflow-visible rounded-3xl bg-gradient-to-r from-blue-800 via-blue-700 to-teal-700 p-6 text-white shadow-lg sm:p-8">
        <div class="relative z-20 flex flex-col gap-4 pr-0 sm:pr-16">

            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-3 py-1 text-xs font-bold text-white">
                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                    SIPALA · Panel Petugas
                </span>

                <h1 class="mt-4 max-w-[230px] text-2xl font-extrabold tracking-tight text-white sm:max-w-none sm:text-3xl">
                    Ringkasan Hari Ini
                </h1>

                <p class="mt-2 text-sm font-medium text-white sm:text-base">
                    Pantau persetujuan peminjaman dan ketersediaan alat sekolah.
                </p>

                <p class="mt-4 text-sm font-semibold text-white">
                    {{ now()->locale('id')->translatedFormat('l, j F Y') }}
                </p>
            </div>

            {{-- IKON NOTIFIKASI --}}
            <div class="absolute right-0 top-4 z-30 flex shrink-0 items-center sm:right-6 sm:top-8"
                    x-data="{ notifikasiBuka: false }">

                <button type="button"
        @click="notifikasiBuka = !notifikasiBuka"
        class="relative flex h-12 w-12 items-center justify-center rounded-2xl border border-white/30 bg-white/15 text-white shadow-md transition hover:bg-white/25"
        aria-label="Notifikasi">

    <svg xmlns="http://www.w3.org/2000/svg"
         class="h-5 w-5 text-white"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor"
         stroke-width="1.8">
        <path stroke-linecap="round"
              stroke-linejoin="round"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
    </svg>

    @if ($jumlahNotifikasi > 0)
        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full border-2 border-blue-800 bg-red-500 px-1 text-[10px] font-extrabold text-white">
            {{ $jumlahNotifikasi > 99 ? '99+' : $jumlahNotifikasi }}
        </span>
    @endif
</button>

                {{-- DROPDOWN NOTIFIKASI --}}
                <div x-cloak
                     x-show="notifikasiBuka"
                     x-transition
                     @click.outside="notifikasiBuka = false"
                     class="absolute right-0 top-full z-[100] mt-3 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl sm:w-96">

                    <div class="border-b border-slate-100 p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-extrabold text-slate-900">
                                    Notifikasi
                                </h3>
                                <p class="mt-1 text-xs text-slate-500">
                                    Informasi keterlambatan pengembalian
                                </p>
                            </div>

                            <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700">
                                {{ $jumlahNotifikasi }} Notifikasi
                            </span>
                        </div>

                        <div class="mt-3 rounded-xl bg-red-50 p-3">
                            <p class="text-xs font-semibold text-red-700">
                                Total denda
                            </p>
                            <p class="mt-1 text-xl font-extrabold text-red-800">
                                Rp{{ number_format($totalDendaNotifikasi, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="max-h-72 overflow-y-auto p-3">
                        @forelse ($notifikasiDenda as $p)
                            <div class="mb-2 rounded-xl border border-slate-100 p-3 last:mb-0">
                                <div class="flex items-start gap-3">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="h-5 w-5"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor"
                                             stroke-width="1.8">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M12 9v3m0 4h.01M10.3 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.7 3.86a2 2 0 00-3.4 0z"/>
                                        </svg>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold text-slate-900">
                                            {{ $p->user->name ?? 'Pengguna' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            @foreach ($p->items as $item)
                                                {{ $item->alat->nama ?? 'Alat' }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </p>

                                        @if ($p->status === 'dipinjam')
                                            <p class="mt-2 text-xs font-semibold text-red-600">
                                                Terlambat {{ $p->hariTerlambatUntukDenda() }} hari
                                            </p>

                                            <p class="mt-1 text-sm font-extrabold text-slate-800">
                                                Rp{{ number_format($p->hitungDenda(), 0, ',', '.') }}
                                            </p>
                                        @else
                                            <p class="mt-2 text-xs font-semibold text-amber-600">
                                                Menunggu pelunasan denda
                                            </p>

                                            <p class="mt-1 text-sm font-extrabold text-slate-800">
                                                Rp{{ number_format($p->total_denda, 0, ',', '.') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 text-center">
                                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-teal-100 text-teal-700">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-6 w-6"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor"
                                         stroke-width="2">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>

                                <p class="mt-3 text-sm font-bold text-slate-800">
                                    Tidak ada notifikasi
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Semua pengembalian dalam kondisi aman.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t border-slate-100 p-3">
                        <a href="{{ route('denda.index') }}"
                           class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-800">
                            Kelola Denda
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- STATISTIK --}}
    <section>
        <div class="mb-4">
            <h2 class="text-lg font-bold text-white">Statistik Inventaris</h2>
            <p class="mt-1 text-sm font-medium text-blue-100">
                Ringkasan kondisi inventaris dan peminjaman.
            </p>
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
                <p class="mt-1 text-sm font-medium text-slate-600">
                    Pengajuan peminjaman yang perlu diproses.
                </p>
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
                                <span class="text-xs font-medium text-slate-600">
                                    · {{ $p->created_at->diffForHumans() }}
                                </span>
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
            <p class="mt-1 text-sm font-medium text-slate-600">
                Pantau alat yang jumlah tersedianya sedikit.
            </p>
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
                <p class="mt-1 text-sm font-medium text-slate-600">
                    Tidak ada alat yang masuk daftar stok menipis.
                </p>
            </div>
        @endif
    </section>

</div>
@endsection
