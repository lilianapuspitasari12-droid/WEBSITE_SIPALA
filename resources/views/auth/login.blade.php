@extends('layouts.tamu')
@section('judul', 'Masuk')

@section('konten')
<div class="min-h-screen grid lg:grid-cols-2 bg-white">

    {{-- BAGIAN KIRI --}}
    <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-navy p-12 text-white">
        <div class="absolute inset-0 opacity-20">
            <img
                src="{{ asset('images/lab-sipala.jpg') }}"
                alt="Laboratorium SIPALA"
                class="h-full w-full object-cover"
                onerror="this.style.display='none'">
        </div>

        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/90 via-blue-950/80 to-blue-800/70"></div>

        <div class="relative z-10">
            @include('komponen-logo', ['terang' => true])
        </div>

        <div class="relative z-10 max-w-lg">
            <span class="mb-5 inline-flex rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm">
                Sistem Peminjaman Alat
            </span>

            <h1 class="mb-5 text-4xl font-bold leading-tight xl:text-5xl">
                Selamat Datang di SIPALA
            </h1>

            <div
    x-data="{
        daftarTeks: [
            'Kelola peminjaman alat sekolah dengan lebih mudah dan praktis.',
            'Pantau status peminjaman alat secara cepat dan teratur.',
            'Proses pengembalian alat menjadi lebih mudah dan transparan.',
            'Semua transaksi peminjaman tercatat dalam satu website.'
        ],
        aktif: 0,
        init() {
            setInterval(() => {
                this.aktif = (this.aktif + 1) % this.daftarTeks.length;
            }, 3500);
        }
    }"
    class="min-h-20"
>
    <p
        x-text="daftarTeks[aktif]"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="text-base leading-7 text-blue-100"
    ></p>

    <div class="mt-5 flex gap-2">
        <template x-for="(teks, index) in daftarTeks" :key="index">
            <button
                type="button"
                @click="aktif = index"
                :class="aktif === index ? 'w-7 bg-white' : 'w-2 bg-white/40'"
                class="h-2 rounded-full transition-all duration-300"
                :aria-label="'Tampilkan teks ' + (index + 1)"
            ></button>
        </template>
    </div>
</div>
        </div>

        <div class="relative z-10 border-t border-white/20 pt-5 text-sm text-blue-100">
            SIPALA | SMK Negeri 5 Surakarta
        </div>
    </div>

    {{-- BAGIAN KANAN --}}
    <div class="flex min-h-screen items-center justify-center bg-slate-50 px-5 py-10 sm:px-10 lg:bg-white">
        <div class="w-full max-w-md">

            {{-- LOGO MOBILE --}}
            <div class="mb-8 flex justify-center lg:hidden">
                @include('komponen-logo')
            </div>

            <div class="mb-8">
                <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-blue-600">
                    SIPALA
                </p>

                <h2 class="text-3xl font-bold tracking-tight text-slate-900">
                    Masuk ke Akun
                </h2>

                <p class="mt-3 text-sm leading-6 text-slate-500">
                    Silakan masukkan nama lengkap, NIS/NIP, dan password
                    untuk melanjutkan.
                </p>
            </div>

            {{-- PESAN BERHASIL --}}
            @if (session('status'))
                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            {{-- PESAN ERROR --}}
            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- NAMA LENGKAP --}}
                <div>
                    <label for="nama_lengkap" class="mb-2 block text-sm font-semibold text-slate-700">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="nama_lengkap"
                        name="nama_lengkap"
                        value="{{ old('nama_lengkap') }}"
                        placeholder="Masukkan nama lengkap"
                        autocomplete="name"
                        required
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                </div>

                {{-- NIS/NIP --}}
                
                <div>
    <label for="login" class="mb-2 block text-sm font-semibold text-slate-700">
        NIS/NIP
    </label>

    <input
        type="text"
        name="login"
        id="login"
        value="{{ old('login') }}"
        inputmode="numeric"
        pattern="[0-9]+"
        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
        placeholder="Masukkan NIS/NIP"
        autocomplete="username"
        required
        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
    >

    @error('login')
        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>


                {{-- PASSWORD --}}
                <div x-data="{ lihat: false }">
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">
                        Password
                    </label>

                    <div class="relative">
                        <input
                            :type="lihat ? 'text' : 'password'"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3.5 pr-14 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">

                        <button type="button"
                                @click="lihat = !lihat"
                                class="absolute inset-y-0 right-0 flex items-center px-4 text-sm font-medium text-slate-500 hover:text-blue-600">
                                <span x-text="lihat ? 'Sembunyikan' : 'Lihat'"></span>
                        </button>
                    </div>
                </div>

                {{-- INGAT SAYA --}}
                <div class="flex items-center justify-between">
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                        <input
                            type="checkbox"
                            name="remember"
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        Ingat saya
                    </label>
                </div>

                {{-- TOMBOL MASUK --}}
                <button
                    type="submit"
                    class="w-full rounded-xl bg-blue-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 active:scale-[0.99]">
                    Masuk Sekarang
                </button>
            </form>

            {{-- DAFTAR --}}
            <p class="mt-7 text-center text-sm text-slate-500">
                Belum punya akun?
                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-blue-600 transition hover:text-blue-800">
                    Buat Akun
                </a>
            </p>

            <p class="mt-10 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} SIPALA SMK Negeri 5 Surakarta.
                Hak Cipta Dilindungi.
            </p>

        </div>
    </div>
</div>
@endsection
