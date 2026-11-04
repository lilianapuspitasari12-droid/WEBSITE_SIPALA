<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0B1224">

    <title>@yield('judul', 'Dashboard') - SIPALA</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        biru: '#3B82F6',
                        tosca: '#14B8A6',
                        tinta: '#E2E8F0',
                        kabut: '#94A3B8',
                        garis: '#263B5B',
                        kertas: '#0B1224'
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif']
                    },
                    boxShadow: {
                        lembut: '0 12px 30px rgba(0, 0, 0, 0.20)'
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            background: #0B1224;
            overflow-x: hidden;
        }

        .angka {
            font-variant-numeric: tabular-nums;
        }

        .menu-link {
            transition: all .2s ease;
        }

        .menu-link:hover {
            transform: translateX(3px);
        }

        .sidebar-gradient {
            background: linear-gradient(
                180deg,
                #080F20 0%,
                #101D3A 38%,
                #16365B 72%,
                #075985 100%
            );
        }

        .dashboard-background {
            background: linear-gradient(
                135deg,
                #080F20 0%,
                #0F1B35 45%,
                #122D4D 100%
            );
        }

        /* Mencegah gambar melewati layar */
        img {
            max-width: 100%;
        }

        /* Tabel tetap dapat digeser di HP */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }

        /* Tampilan HP */
        @media (max-width: 640px) {
            body {
                font-size: 14px;
            }

            main {
                width: 100%;
                min-width: 0;
            }

            main > div {
                padding-left: 14px !important;
                padding-right: 14px !important;
                padding-top: 76px !important;
            }

            input,
            select,
            textarea {
                max-width: 100%;
            }

            table {
                min-width: 600px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>

<body
    class="min-h-screen dashboard-background font-sans text-slate-100 antialiased"
    x-data="{
        menuBuka: false,
        sidebarKecil: false
    }"
    @keydown.escape.window="menuBuka = false"
>

@php
    $user = auth()->user();
    $petugas = $user->isAdmin();

    $notifikasiDenda = collect();

    if ($petugas) {
        $notifikasiDenda = \App\Models\Peminjaman::where(function ($query) {
            $query->where(function ($q) {
                $q->where('status', 'dipinjam')
                    ->whereDate('tanggal_jatuh_tempo', '<', today());
            })->orWhere(function ($q) {
                $q->where('status', 'dikembalikan')
                    ->where('total_denda', '>', 0)
                    ->where('status_denda', '!=', 'lunas');
            });
        })->get();
    }

    $jumlahNotifikasiDenda = $notifikasiDenda->count();

    $totalDendaNotifikasi = $notifikasiDenda->sum(function ($peminjaman) {
        return $peminjaman->status === 'dikembalikan'
            ? (int) $peminjaman->total_denda
            : $peminjaman->hitungDenda();
    });
@endphp

<div class="min-h-screen">

    {{-- TOMBOL MENU HP --}}
<button
    type="button"
    x-show="!menuBuka"
    x-cloak
    @click="menuBuka = true"
    class="fixed left-4 top-4 z-[60] flex h-11 w-11 items-center justify-center rounded-xl border border-white/15 bg-slate-900/95 text-xl text-white shadow-lg backdrop-blur transition hover:bg-blue-700 lg:hidden"
    aria-label="Buka menu"
>
    ☰
</button>
    {{-- LATAR BELAKANG MENU HP --}}
    <div
        x-cloak
        x-show="menuBuka"
        x-transition.opacity
        @click="menuBuka = false"
        class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm lg:hidden"
    ></div>

    {{-- SIDEBAR --}}
    <aside
        :class="[
            menuBuka ? 'translate-x-0' : '-translate-x-full',
            sidebarKecil ? 'lg:w-20' : 'lg:w-64'
        ]"
        class="sidebar-gradient fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-white/10 shadow-2xl shadow-black/30 transition-all duration-300 lg:translate-x-0"
    >

        {{-- LOGO --}}
        <div class="relative flex items-center justify-between border-b border-white/10 bg-white/[0.03] px-4 py-5">

            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">

                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-cyan-400 via-blue-500 to-blue-700 text-white shadow-lg shadow-blue-950/50 ring-1 ring-white/20">

                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m4.5 7.7 7.5 4.4 7.5-4.4M12 12v8.5M8 5.3l8 4.5"/>
                    </svg>

                </span>

                <span x-show="!sidebarKecil" x-transition>
                    <span class="block text-lg font-extrabold tracking-tight text-white">
                        SIPALA
                    </span>

                    <span class="block text-[9px] font-bold uppercase tracking-widest text-cyan-300">
                        Peminjaman Alat
                    </span>
                </span>

            </a>

            {{-- KECILKAN SIDEBAR LAPTOP --}}
            <button
                type="button"
                @click="sidebarKecil = !sidebarKecil"
                class="absolute -right-4 top-5 z-50 hidden h-9 w-9 items-center justify-center rounded-xl border border-white/15 bg-slate-800 text-sm text-cyan-200 shadow-lg hover:bg-blue-700 lg:flex"
                :aria-label="sidebarKecil ? 'Perbesar sidebar' : 'Kecilkan sidebar'"
                x-text="sidebarKecil ? '☰' : '×'"
            ></button>

            {{-- TUTUP SIDEBAR HP --}}
            <button
                type="button"
                @click="menuBuka = false"
                class="rounded-lg p-2 text-white/60 hover:bg-white/10 hover:text-white lg:hidden"
                aria-label="Tutup menu"
            >
                ✕
            </button>

        </div>

        {{-- NAVIGASI --}}
        <nav class="flex-1 space-y-1 overflow-y-auto overscroll-contain px-3 py-5">

            {{-- PROFIL --}}
            <a
                href="{{ route('profil.edit') }}"
                title="Profil Saya"
                @click="menuBuka = false"
                :class="sidebarKecil ? 'justify-center' : ''"
                class="menu-link mb-6 flex items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.07] p-3 shadow-inner shadow-white/[0.02] hover:bg-white/[0.13]"
            >

                <div class="flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-cyan-400 to-blue-600 font-bold text-white shadow-lg ring-2 ring-white/15">

                    @if ($user->foto_profil)
                        <img
                            src="{{ asset('storage/' . $user->foto_profil) }}"
                            alt="Foto profil"
                            class="h-full w-full object-cover"
                        >
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif

                </div>

                <div x-show="!sidebarKecil" x-transition class="min-w-0">

                    <p class="truncate text-xs font-bold text-white">
                        {{ $user->name }}
                    </p>

                    <p class="truncate text-[10px] text-blue-100/70">
                        {{ $user->kelas_jabatan ?: $user->roleLabel() }}
                    </p>

                    <span class="mt-1 inline-block rounded-full border border-cyan-300/20 bg-cyan-300/10 px-2 py-0.5 text-[9px] font-semibold text-cyan-200">
                        {{ $user->roleLabel() }}
                    </span>

                </div>

            </a>

            {{-- JUDUL MENU --}}
            <p
                x-show="!sidebarKecil"
                class="mb-2 px-3 text-[10px] font-bold uppercase tracking-widest text-blue-200/50"
            >
                {{ $petugas ? 'Menu Petugas' : 'Area Siswa' }}
            </p>

            {{-- DASHBOARD --}}
            <a
                href="{{ route('dashboard') }}"
                title="Dashboard"
                @click="menuBuka = false"
                class="menu-link flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold
                {{ request()->routeIs('dashboard')
                    ? 'bg-gradient-to-r from-blue-500 to-cyan-500 text-white shadow-lg shadow-blue-950/40 ring-1 ring-white/20'
                    : 'text-blue-50/80 hover:bg-white/10 hover:text-white' }}"
            >
                <span class="w-5 shrink-0 text-center text-lg">▦</span>
                <span x-show="!sidebarKecil" x-transition>Dashboard</span>
            </a>

            {{-- PEMINJAMAN --}}
            <a
                href="{{ route('peminjaman.index') }}"
                title="Peminjaman"
                @click="menuBuka = false"
                class="menu-link flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium
                {{ request()->routeIs('peminjaman.*')
                    ? 'bg-gradient-to-r from-blue-500 to-cyan-500 font-semibold text-white shadow-lg shadow-blue-950/40 ring-1 ring-white/20'
                    : 'text-blue-50/80 hover:bg-white/10 hover:text-white' }}"
            >
                <span class="w-5 shrink-0 text-center text-lg">▣</span>
                <span x-show="!sidebarKecil" x-transition>
                    {{ $petugas ? 'Semua Peminjaman' : 'Peminjaman' }}
                </span>
            </a>

            {{-- PERSETUJUAN / PENGEMBALIAN --}}
            @if ($petugas)

                @if (\Illuminate\Support\Facades\Route::has('persetujuan.index'))

                    <a
                        href="{{ route('persetujuan.index') }}"
                        title="Persetujuan dan Pengembalian"
                        @click="menuBuka = false"
                        class="menu-link flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium
                        {{ request()->routeIs('persetujuan.*')
                            ? 'bg-gradient-to-r from-blue-500 to-cyan-500 font-semibold text-white shadow-lg shadow-blue-950/40 ring-1 ring-white/20'
                            : 'text-blue-50/80 hover:bg-white/10 hover:text-white' }}"
                    >
                        <span class="w-5 shrink-0 text-center text-lg">☑</span>

                        <span x-show="!sidebarKecil" x-transition>
                            Persetujuan & Pengembalian
                        </span>
                    </a>

                @endif

            @else

                <a
                    href="{{ route('pengembalian.index') }}"
                    title="Pengembalian"
                    @click="menuBuka = false"
                    class="menu-link flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium
                    {{ request()->routeIs('pengembalian.*')
                        ? 'bg-gradient-to-r from-blue-500 to-cyan-500 font-semibold text-white shadow-lg shadow-blue-950/40 ring-1 ring-white/20'
                        : 'text-blue-50/80 hover:bg-white/10 hover:text-white' }}"
                >
                    <span class="w-5 shrink-0 text-center text-lg">↩</span>
                    <span x-show="!sidebarKecil" x-transition>Pengembalian</span>
                </a>

            @endif

            {{-- RIWAYAT --}}
            <a
                href="{{ route('riwayat.index') }}"
                title="Riwayat Peminjaman"
                @click="menuBuka = false"
                class="menu-link flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium
                {{ request()->routeIs('riwayat.*')
                    ? 'bg-gradient-to-r from-blue-500 to-cyan-500 font-semibold text-white shadow-lg shadow-blue-950/40 ring-1 ring-white/20'
                    : 'text-blue-50/80 hover:bg-white/10 hover:text-white' }}"
            >
                <span class="w-5 shrink-0 text-center text-lg">◷</span>

                <span x-show="!sidebarKecil" x-transition>
                    Riwayat Peminjaman
                </span>
            </a>

            {{-- PENGATURAN ADMIN --}}
            @if ($petugas)

                <div class="my-5 border-t border-white/10"></div>

                <p
                    x-show="!sidebarKecil"
                    class="mb-2 px-3 text-[10px] font-bold uppercase tracking-widest text-blue-200/50"
                >
                    Pengaturan
                </p>

                {{-- KELOLA ALAT --}}
                <a
                    href="{{ route('alat.index') }}"
                    title="Kelola Data Alat"
                    @click="menuBuka = false"
                    class="menu-link flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium
                    {{ request()->routeIs('alat.*')
                        ? 'bg-gradient-to-r from-blue-500 to-cyan-500 font-semibold text-white shadow-lg shadow-blue-950/40 ring-1 ring-white/20'
                        : 'text-blue-50/80 hover:bg-white/10 hover:text-white' }}"
                >
                    <span class="w-5 shrink-0 text-center text-lg">⚙</span>
                    <span x-show="!sidebarKecil" x-transition>Kelola Data Alat</span>
                </a>

                {{-- REKAP PEMINJAMAN --}}
                <a
                    href="{{ route('laporan.peminjaman') }}"
                    title="Rekap Peminjaman"
                    @click="menuBuka = false"
                    class="menu-link flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium
                    {{ request()->routeIs('laporan.peminjaman*')
                        ? 'bg-gradient-to-r from-blue-500 to-cyan-500 font-semibold text-white shadow-lg shadow-blue-950/40 ring-1 ring-white/20'
                        : 'text-blue-50/80 hover:bg-white/10 hover:text-white' }}"
                >
                    <span class="w-5 shrink-0 text-center text-lg">▤</span>

                    <span x-show="!sidebarKecil" x-transition>
                        Rekap Peminjaman
                    </span>
                </a>

            @endif

            <div class="my-5 border-t border-white/10"></div>

            {{-- KELUAR --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button
                    type="submit"
                    title="Keluar"
                    class="menu-link flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm font-semibold text-rose-200 hover:bg-rose-400/10 hover:text-rose-100"
                >
                    <span class="w-5 shrink-0 text-center text-lg">↪</span>
                    <span x-show="!sidebarKecil" x-transition>Keluar</span>
                </button>
            </form>

        </nav>

        {{-- FOOTER SIDEBAR --}}
        <div
            x-show="!sidebarKecil"
            class="border-t border-white/10 bg-slate-950/20 px-6 py-4"
        >
            <p class="text-[10px] font-medium text-blue-100/60">
                © 2026 SIPALA
            </p>

            <p class="text-[10px] text-blue-100/40">
                SMK Negeri 5 Surakarta
            </p>
        </div>

    </aside>

    {{-- AREA KONTEN --}}
    <main
        :class="sidebarKecil ? 'lg:ml-20' : 'lg:ml-64'"
        class="min-h-screen min-w-0 flex-1 transition-all duration-300"
    >

        <div class="mx-auto w-full max-w-[1500px] min-w-0 px-4 py-6 pt-20 sm:px-6 lg:px-8 lg:py-8">

           
            {{-- NOTIFIKASI BERHASIL --}}
            @if (session('sukses'))
                <div class="mb-5 rounded-xl border border-emerald-400/20 bg-gradient-to-r from-emerald-950/80 to-teal-950/80 px-4 py-3 text-sm font-medium text-emerald-200 shadow-sm">
                    ✓ {{ session('sukses') }}
                </div>
            @endif

            {{-- NOTIFIKASI GAGAL --}}
            @if (session('gagal'))
                <div class="mb-5 rounded-xl border border-rose-400/20 bg-gradient-to-r from-rose-950/80 to-orange-950/80 px-4 py-3 text-sm font-medium text-rose-200 shadow-sm">
                    ! {{ session('gagal') }}
                </div>
            @endif

            {{-- KONTEN HALAMAN --}}
            @yield('konten')

            {{-- FOOTER UTAMA --}}
            <footer class="mt-10 border-t border-white/10 pt-4 text-center text-[10px] leading-relaxed text-blue-100/40">
                SIPALA · Sistem Peminjaman Alat Praktik · SMK Negeri 5 Surakarta
            </footer>

        </div>
    </main>

</div>

</body>
</html>