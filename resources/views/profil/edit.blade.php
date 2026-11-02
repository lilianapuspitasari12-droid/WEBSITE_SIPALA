@extends('layouts.app')
@section('judul', 'Profil Saya')

@section('konten')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-teal-500 p-6 text-white shadow-lg">
        <div class="relative z-10">
            <span class="text-xs font-semibold uppercase tracking-widest text-blue-100">
                Pengaturan Akun
            </span>
            <h1 class="mt-2 text-2xl font-bold md:text-3xl">
                Profil Saya
            </h1>
            <p class="mt-2 text-sm text-blue-50">
                Kelola informasi pribadi, foto profil, dan keamanan akun SIPALA.
            </p>
        </div>
        <div class="absolute -right-8 -top-12 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute -bottom-16 right-24 h-36 w-36 rounded-full bg-white/10"></div>
    </div>

    {{-- ERROR VALIDASI --}}
    @if ($errors->any())
        <div class="rounded-2xl border border-rose-400/30 bg-rose-950/50 p-4 text-sm text-rose-200">
            <p class="mb-2 font-bold">Periksa kembali data berikut:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('profil.update') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >
        @csrf
        @method('PUT')

        
{{-- FOTO PROFIL --}}
<section
    x-data='{
        preview: @js($user->foto_profil ? asset("storage/" . $user->foto_profil) : ""),
        pilihFoto(event) {
            const file = event.target.files[0];

            if (file) {
                if (this.preview.startsWith("blob:")) {
                    URL.revokeObjectURL(this.preview);
                }

                this.preview = URL.createObjectURL(file);
            }
        }
    }'
    class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl"
>
    <div class="mb-5">
        <h2 class="text-lg font-bold text-white">Foto Profil</h2>
        <p class="mt-1 text-sm text-slate-400">
            Gunakan foto JPG, PNG, atau WEBP maksimal 2 MB.
        </p>
    </div>

    <div class="flex flex-col items-center gap-5 sm:flex-row">

        {{-- PREVIEW FOTO --}}
        <div class="flex h-28 w-28 shrink-0 items-center justify-center overflow-hidden rounded-full border-4 border-cyan-400/30 bg-gradient-to-br from-cyan-400 to-blue-700 text-4xl font-bold text-white shadow-xl">

            <template x-if="preview">
                <img
                    :src="preview"
                    alt="Foto Profil"
                    class="h-full w-full object-cover"
                >
            </template>

            <template x-if="!preview">
                <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </template>

        </div>

        <div class="flex flex-wrap justify-center gap-3 sm:justify-start">

            <label class="cursor-pointer rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                Pilih Foto

                <input
                    type="file"
                    name="foto_profil"
                    accept=".jpg,.jpeg,.png,.webp"
                    class="hidden"
                    @change="pilihFoto($event)"
                >
            </label>

            @if ($user->foto_profil)
                <button
                    type="submit"
                    form="hapusFotoForm"
                    class="rounded-xl border border-rose-400/30 px-5 py-3 text-sm font-semibold text-rose-300 transition hover:bg-rose-500/10"
                    onclick="return confirm('Yakin ingin menghapus foto profil?')"
                >
                    Hapus Foto
                </button>
            @endif

        </div>
    </div>

    <p class="mt-3 text-center text-xs text-slate-400 sm:text-left">
        Foto akan langsung terlihat setelah dipilih. Tekan Simpan Perubahan untuk menyimpannya.
    </p>
</section>


        {{-- INFORMASI PRIBADI --}}
        <section class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-white">Informasi Pribadi</h2>
                <p class="mt-1 text-sm text-slate-400">
                    Perbarui informasi akun kamu.
                </p>
            </div>

            <div class="grid gap-5 md:grid-cols-2">

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">
                        Nama Lengkap
                    </label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        class="w-full rounded-xl border border-white/10 bg-slate-800 px-4 py-3 text-sm text-white outline-none transition focus:border-cyan-400"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">
                        NIS/NIP
                    </label>
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username', $user->username) }}"
                        required
                        class="w-full rounded-xl border border-white/10 bg-slate-800 px-4 py-3 text-sm text-white outline-none transition focus:border-cyan-400"
                    >
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">
                        Nomor HP
                    </label>
                    <input
                        type="text"
                        name="nomor_hp"
                        value="{{ old('nomor_hp', $user->nomor_hp) }}"
                        placeholder="Masukkan nomor HP"
                        class="w-full rounded-xl border border-white/10 bg-slate-800 px-4 py-3 text-sm text-white outline-none transition focus:border-cyan-400"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">
                        {{ $user->role === 'siswa' ? 'Kelas' : ($user->role === 'guru' ? 'Jabatan' : 'Kelas / Jabatan') }}
                    </label>
                    <input
                        type="text"
                        name="kelas_jabatan"
                        value="{{ old('kelas_jabatan', $user->kelas_jabatan) }}"
                        placeholder="Masukkan kelas atau jabatan"
                        class="w-full rounded-xl border border-white/10 bg-slate-800 px-4 py-3 text-sm text-white outline-none transition focus:border-cyan-400"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">
                        Peran Pengguna
                    </label>
                    <input
                        type="text"
                        value="{{ $user->roleLabel() }}"
                        readonly
                        class="w-full rounded-xl border border-white/10 bg-slate-950 px-4 py-3 text-sm text-slate-400"
                    >
                    <p class="mt-1 text-xs text-slate-500">
                        Peran akun hanya dapat dikelola oleh sistem/Admin.
                    </p>
                </div>

            </div>
        </section>


{{-- GANTI PASSWORD --}}
<div
    x-data="{ tampilPassword: false }"
    class="rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-xl"
>
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h2 class="text-lg font-bold text-white">
                Keamanan Akun
            </h2>
            <p class="mt-1 text-sm text-slate-400">
                Ubah password jika ingin memperbarui keamanan akun.
            </p>
        </div>

        <button
            type="button"
            @click="tampilPassword = !tampilPassword"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-950/30 transition hover:bg-blue-700"
        >
            <span x-text="tampilPassword ? 'Batal Ganti Password' : 'Ganti Password'"></span>

            <svg
                class="h-4 w-4 transition-transform"
                :class="tampilPassword ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </button>
    </div>

    <div
        x-show="tampilPassword"
        x-transition
        x-cloak
        class="mt-6 space-y-5 border-t border-white/10 pt-6"
    >
        <div class="rounded-xl border border-cyan-400/20 bg-cyan-400/10 p-4 text-sm text-cyan-200">
            Masukkan password lama untuk memastikan bahwa kamu pemilik akun.
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-200">
                Password Lama
            </label>
            <input
                type="password"
                name="password_lama"
                autocomplete="current-password"
                class="w-full rounded-xl border border-white/10 bg-slate-800 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-400"
                placeholder="Masukkan password lama"
            >
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-200">
                Password Baru
            </label>
            <input
                type="password"
                name="password_baru"
                autocomplete="new-password"
                class="w-full rounded-xl border border-white/10 bg-slate-800 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-400"
                placeholder="Minimal 8 karakter"
            >
        </div>

        <div>
            <label class="mb-2 block text-sm font-semibold text-slate-200">
                Konfirmasi Password Baru
            </label>
            <input
                type="password"
                name="password_baru_confirmation"
                autocomplete="new-password"
                class="w-full rounded-xl border border-white/10 bg-slate-800 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-500 focus:border-cyan-400"
                placeholder="Ulangi password baru"
            >
        </div>
    </div>
</div>


        {{-- TOMBOL SIMPAN --}}
        <div class="flex flex-wrap justify-end gap-3">
            <a
                href="{{ route('dashboard') }}"
                class="rounded-xl border border-white/15 px-6 py-3 text-sm font-semibold text-slate-300 transition hover:bg-white/10"
            >
                Kembali
            </a>

            <button
                type="submit"
                class="rounded-xl bg-gradient-to-r from-blue-600 to-teal-500 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-blue-950/30 transition hover:brightness-110"
            >
                Simpan Perubahan
            </button>
        </div>

    </form>

    {{-- FORM HAPUS FOTO --}}
    <form
        id="hapusFotoForm"
        action="{{ route('profil.foto.hapus') }}"
        method="POST"
        class="hidden"
    >
        @csrf
        @method('DELETE')
    </form>

</div>
@endsection