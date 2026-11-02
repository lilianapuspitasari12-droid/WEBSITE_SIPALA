@extends('layouts.tamu')
@section('judul', 'Pilih Role')

@section('konten')
<div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-slate-950 via-blue-950 to-blue-900 px-5 py-12">

    {{-- HIASAN BACKGROUND --}}
    <div class="absolute -left-24 -top-24 h-80 w-80 rounded-full bg-blue-500/10 blur-3xl"></div>
    <div class="absolute -bottom-24 -right-24 h-80 w-80 rounded-full bg-teal-400/10 blur-3xl"></div>

    <div class="relative z-10 w-full max-w-2xl">

        {{-- LOGO --}}
        <div class="mb-10 flex justify-center">
            @include('komponen-logo', ['terang' => true])
        </div>

        {{-- JUDUL --}}
        <div class="mb-10 text-center">
            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold tracking-wider text-cyan-200">
                SISTEM PEMINJAMAN ALAT
            </span>

            <h1 class="mt-5 text-3xl font-extrabold text-white sm:text-4xl">
                Selamat Datang di SIPALA
            </h1>

            <p class="mx-auto mt-4 max-w-lg text-sm leading-7 text-blue-100/80">
                Silakan pilih jenis akun untuk melanjutkan ke website
                Sistem Peminjaman Alat SMK Negeri 5 Surakarta.
            </p>
        </div>

        {{-- PILIHAN ROLE --}}
        <div class="space-y-5">

            {{-- ADMIN --}}
            <a href="{{ route('login') }}"
               class="group flex w-full items-center gap-5 rounded-2xl border border-white/20 bg-white p-6 shadow-xl transition duration-300 hover:-translate-y-1 hover:border-blue-300 hover:shadow-blue-500/20">

                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-white shadow-lg shadow-blue-200">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3 4 7v5c0 5 3.5 8 8 9 4.5-1 8-4 8-9V7l-8-4Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m9 12 2 2 4-4"/>
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Admin / Petugas
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Masuk menggunakan akun administrator SIPALA.
                    </p>
                    <span class="mt-3 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-bold text-white transition group-hover:bg-blue-700">
                        Masuk sebagai Admin
                        <span>→</span>
                    </span>
                </div>
            </a>

            {{-- GURU DAN SISWA --}}
            <a href="{{ route('register') }}"
               class="group flex w-full items-center gap-5 rounded-2xl border border-white/20 bg-white p-6 shadow-xl transition duration-300 hover:-translate-y-1 hover:border-teal-300 hover:shadow-teal-500/20">

                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-teal-400 to-emerald-600 text-white shadow-lg shadow-teal-200">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="10" cy="7" r="4" stroke-width="1.8"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 8v6m-3-3h6"/>
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <h2 class="text-lg font-extrabold text-slate-900">
                        Guru / Siswa
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Daftar akun baru atau lanjutkan sebagai pengguna SIPALA.
                    </p>
                    <span class="mt-3 inline-flex items-center gap-2 rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-bold text-white transition group-hover:bg-teal-700">
                        Lanjutkan
                        <span>→</span>
                    </span>
                </div>
            </a>

        </div>

        {{-- FOOTER --}}
        <p class="mt-10 text-center text-xs text-blue-100/50">
            © {{ date('Y') }} SIPALA · SMK Negeri 5 Surakarta.
            Hak Cipta Dilindungi.
        </p>

    </div>
</div>
@endsection