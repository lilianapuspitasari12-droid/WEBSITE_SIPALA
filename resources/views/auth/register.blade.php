@extends('layouts.tamu')
@section('judul', 'Buat Akun')

@section('konten')
<div class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 px-4 py-10 sm:px-6 lg:py-14">
    <div class="mx-auto w-full max-w-2xl">

        {{-- LOGO --}}
        <div class="mb-8 flex justify-start">
    <div class="rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-white shadow-lg backdrop-blur-sm">
        @include('komponen-logo', ['terang' => true])
    </div>
</div>

        {{-- KARTU PENDAFTARAN --}}
        <div class="overflow-hidden rounded-3xl border border-white/10 bg-white shadow-2xl shadow-black/20">

            {{-- HEADER NAVY --}}
            <div class="relative overflow-hidden bg-slate-900 px-6 py-8 text-white sm:px-10">
                <div class="absolute -right-12 -top-16 h-48 w-48 rounded-full bg-blue-600/20 blur-2xl"></div>
                <div class="absolute -bottom-16 left-1/3 h-40 w-40 rounded-full bg-blue-400/10 blur-2xl"></div>

                <div class="relative">
                    <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-medium text-blue-100">
                        SIPALA · SMK Negeri 5 Surakarta
                    </span>

                    <h1 class="mt-5 text-2xl font-bold sm:text-3xl">
                        Buat Akun Baru
                    </h1>

                    <p class="mt-2 max-w-lg text-sm leading-6 text-slate-300">
                        Lengkapi data diri kamu untuk mulai menggunakan
                        sistem peminjaman dan pengembalian alat sekolah.
                    </p>
                </div>
            </div>

            {{-- FORM --}}
            <div class="p-6 sm:p-10">
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <p class="font-semibold">Pendaftaran belum berhasil.</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('register') }}"
                    class="space-y-5"
                    x-data="{
                        peran: '{{ old('role', 'siswa') }}',
                        lihat: false,
                        lihatUlang: false
                    }"
                >
                    @csrf

                    {{-- NAMA LENGKAP --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">
                            Nama Lengkap
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Masukkan nama lengkap"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10"
                        >

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NIS/NIP --}}
                    <div>
    <label for="username" class="mb-2 block text-sm font-semibold text-slate-700">
        NIS/NIP
    </label>

    <input
        type="text"
        name="username"
        id="username"
        value="{{ old('username') }}"
        inputmode="numeric"
        pattern="[0-9]+"
        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
        placeholder="Masukkan NIS/NIP"
        required
        class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
    >

    @error('username')
        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>

                    {{-- NOMOR HP --}}
                    <div>
    <label for="nomor_hp" class="mb-2 block text-sm font-semibold text-slate-700">
        Nomor HP
    </label>

    <input
        type="tel"
        name="nomor_hp"
        id="nomor_hp"
        value="{{ old('nomor_hp') }}"
        inputmode="numeric"
        pattern="[0-9]+"
        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
        placeholder="Masukkan nomor HP"
        required
        class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
    >

    @error('nomor_hp')
        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>

                    {{-- KELAS / JABATAN --}}
                    <div>
                        <label for="kelas_jabatan" class="mb-2 block text-sm font-semibold text-slate-700">
                            Kelas / Jabatan
                        </label>

                        <input
                            id="kelas_jabatan"
                            type="text"
                            name="kelas_jabatan"
                            value="{{ old('kelas_jabatan') }}"
                            required
                            placeholder="Contoh: XII RPL A atau Guru RPL"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10"
                        >

                        @error('kelas_jabatan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                   
{{-- STATUS PENGGUNA --}}
<div>
    <span class="mb-2 block text-sm font-semibold text-slate-700">
        Status Pengguna
    </span>

    <input type="hidden" name="role" :value="peran">

    <div class="grid grid-cols-2 gap-3">
        <button
            type="button"
            @click="peran = 'siswa'"
            :aria-pressed="peran === 'siswa'"
            :class="peran === 'siswa'
                ? 'border-blue-600 bg-blue-50 text-blue-700 ring-2 ring-blue-600/20'
                : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50'"
            class="rounded-xl border px-4 py-4 text-sm font-semibold transition"
        >
            <span class="block">Siswa</span>
        </button>

        <button
            type="button"
            @click="peran = 'guru'"
            :aria-pressed="peran === 'guru'"
            :class="peran === 'guru'
                ? 'border-blue-600 bg-blue-50 text-blue-700 ring-2 ring-blue-600/20'
                : 'border-slate-200 bg-white text-slate-500 hover:bg-slate-50'"
            class="rounded-xl border px-4 py-4 text-sm font-semibold transition"
        >
            <span class="block">Guru / Staf</span>
        </button>
    </div>

    @error('role')
        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

                    {{-- PASSWORD --}}
                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">
                            Password
                        </label>

                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                x-bind:type="lihat ? 'text' : 'password'"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                placeholder="Buat password minimal 8 karakter"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-24 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10"
                            >

                            <button
                                type="button"
                                @click="lihat = !lihat"
                                x-text="lihat ? 'Sembunyikan' : 'Lihat'"
                                class="absolute inset-y-0 right-0 px-4 text-xs font-semibold text-blue-600 hover:text-blue-800"
                            ></button>
                        </div>

                        <p class="mt-2 text-xs text-slate-500">
                            Gunakan password minimal 8 karakter.
                        </p>

                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ULANGI PASSWORD --}}
                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">
                            Ulangi Password
                        </label>

                        <div class="relative">
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                x-bind:type="lihatUlang ? 'text' : 'password'"
                                required
                                minlength="8"
                                autocomplete="new-password"
                                placeholder="Masukkan password yang sama"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 pr-24 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10"
                            >

                            <button
                                type="button"
                                @click="lihatUlang = !lihatUlang"
                                x-text="lihatUlang ? 'Sembunyikan' : 'Lihat'"
                                class="absolute inset-y-0 right-0 px-4 text-xs font-semibold text-blue-600 hover:text-blue-800"
                            ></button>
                        </div>
                    </div>

                    {{-- TOMBOL DAFTAR --}}
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-blue-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/20 active:scale-[0.99]"
                    >
                        Daftar Akun
                    </button>
                </form>

                
{{-- LINK LOGIN --}}
<p class="mt-7 text-center text-sm text-slate-500">
    Sudah mempunyai akun?
    <a
        href="{{ route('login', ['role' => request('role', old('role', 'siswa'))]) }}"
        class="ml-1 font-semibold text-blue-600 hover:text-blue-800"
    >
        Masuk
    </a>
</p>


        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} SIPALA SMK Negeri 5 Surakarta.
            Hak Cipta Dilindungi.
        </p>
    </div>
</div>
@endsection