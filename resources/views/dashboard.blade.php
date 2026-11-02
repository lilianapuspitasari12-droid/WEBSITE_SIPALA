
@extends('layouts.app')
@section('judul', 'Dashboard Siswa')

@section('konten')

@php
    $menunggu = $aktif->where('status', 'menunggu')->count();

    $sedangDipinjam = $aktif->whereIn('status', [
        'disetujui', 'dipinjam'
    ])->count();

    $selesai = $riwayat->where('status', 'dikembalikan')->count();

    $terlambat = $aktif->filter(
        fn ($p) => $p->isTerlambat()
    )->count();

    $aktivitas = $aktif->concat($riwayat)
        ->unique('id')
        ->sortByDesc('created_at')
        ->take(4);

    // Mengambil 5 notifikasi terbaru pengguna.
    $daftarNotifikasi = auth()->user()
        ->notifications()
        ->latest()
        ->take(5)
        ->get();

    // Menghitung notifikasi yang belum dibaca.
    $jumlahNotifikasi = auth()->user()
        ->unreadNotifications()
        ->count();
@endphp


{{-- HEADER DASHBOARD --}}
<div class="relative mb-7 overflow-visible rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-teal-500 p-6 text-white shadow-lg">

    <div class="relative z-10 flex flex-wrap items-start justify-between gap-5">

        {{-- JUDUL DASHBOARD --}}
        <div class="max-w-2xl">
            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[11px] font-semibold tracking-wide text-blue-50">
                SIPALA • Sistem Peminjaman Alat
            </span>

            <h1 class="mt-3 text-2xl font-bold md:text-3xl">
                Dashboard {{ auth()->user()->isAdmin() ? 'Petugas' : 'Siswa' }}
            </h1>

            <p class="mt-2 text-sm leading-6 text-blue-50">
                Selamat datang kembali, {{ auth()->user()->name }}!
                Pantau aktivitas dan status peminjaman alat praktik melalui dashboard SIPALA.
            </p>
            <p class="mt-2 text-sm text-blue-100"> {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</p>
        </div>

        {{-- NOTIFIKASI DAN BANTUAN --}}
        <div class="flex items-center gap-2">

            {{-- NOTIFIKASI --}}
            <div class="relative" x-data="{ notifikasiBuka: false }">

                <button
                    type="button"
                    @click="notifikasiBuka = !notifikasiBuka"
                    title="Notifikasi"
                    class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-white/20 bg-white text-slate-800 shadow-sm transition hover:bg-blue-50">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 00-4.5-5.8V5a1.5 1.5 0 00-3 0v.2A6 6 0 006 11v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 01-6 0"/>
                    </svg>

                    @if ($jumlahNotifikasi > 0)
                        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full border-2 border-white bg-red-500 px-1 text-[10px] font-bold text-white">
                            {{ $jumlahNotifikasi > 99 ? '99+' : $jumlahNotifikasi }}
                        </span>
                    @endif
                </button>

                {{-- DROPDOWN NOTIFIKASI --}}
                <div
                    x-cloak
                    x-show="notifikasiBuka"
                    @click.outside="notifikasiBuka = false"
                    x-transition
                    class="absolute right-0 z-50 mt-3 w-[340px] max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-800 shadow-2xl">

                    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Notifikasi
                            </h3>
                            <p class="mt-1 text-[11px] text-slate-500">
                                Informasi terbaru peminjaman kamu
                            </p>
                        </div>

                        @if ($jumlahNotifikasi > 0)
                            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-bold text-blue-700">
                                {{ $jumlahNotifikasi }} belum dibaca
                            </span>
                        @endif
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        @forelse ($daftarNotifikasi as $notif)

                            <a href="{{ route('notifikasi.show', $notif->id) }}"
                                class="flex gap-3 border-b border-slate-100 px-4 py-4 transition hover:bg-blue-50 {{ is_null($notif->read_at) ? 'bg-blue-50/70' : 'bg-white' }}">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                                    {{ ($notif->data['jenis'] ?? '') === 'terlambat'
                                        ? 'bg-red-100 text-red-600'
                                        : ((($notif->data['jenis'] ?? '') === 'pengingat')
                                            ? 'bg-amber-100 text-amber-600'
                                            : 'bg-blue-100 text-blue-600') }}">

                                    @if (($notif->data['jenis'] ?? '') === 'terlambat')
                                        <span class="text-lg font-bold">!</span>
                                    @elseif (($notif->data['jenis'] ?? '') === 'pengingat')
                                        <span class="text-lg">◷</span>
                                    @else
                                        <span class="text-lg">✓</span>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-xs font-bold text-slate-900">
                                            {{ $notif->data['judul'] ?? 'Informasi Peminjaman' }}
                                        </p>

                                        @if (is_null($notif->read_at))
                                            <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-600"></span>
                                        @endif
                                    </div>

                                    <p class="mt-1 text-[11px] leading-5 text-slate-600">
                                        {{ $notif->data['pesan'] ?? 'Ada informasi terbaru mengenai peminjaman kamu.' }}
                                    </p>

                                    @if (!empty($notif->data['kode']))
                                        <p class="mt-1 text-[10px] font-semibold text-blue-600">
                                            {{ $notif->data['kode'] }}
                                        </p>
                                    @endif

                                    <p class="mt-2 text-[10px] text-slate-400">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </a>

                        @empty
                            <div class="px-5 py-10 text-center">
                                <div class="mb-3 text-3xl">♧</div>
                                <p class="text-sm font-bold text-slate-800">
                                    Belum Ada Notifikasi
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Informasi peminjaman akan muncul di sini.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t border-slate-100 bg-slate-50 px-4 py-3">
                        <a href="{{ route('peminjaman.index') }}"
                            class="block text-center text-xs font-bold text-blue-600 hover:text-blue-800">
                            Lihat Peminjaman Saya →
                        </a>
                    </div>
                </div>
            </div>

            {{-- BANTUAN --}}
<div class="relative">
    <button
        type="button"
        title="Bantuan"
        onclick="document.getElementById('menuBantuan').classList.toggle('hidden')"
        class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/20 bg-white text-lg font-bold text-slate-800 shadow-sm transition hover:bg-blue-50">
        ?
    </button>

    <div id="menuBantuan"
        class="hidden absolute right-0 top-14 z-50 w-[350px] max-h-[75vh] overflow-y-auto rounded-2xl border border-slate-100 bg-white p-5 text-slate-700 shadow-2xl">

        {{-- HEADER --}}
        <div class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Pusat Bantuan</h3>
                <p class="text-xs text-slate-500">Panduan penggunaan SIPALA</p>
            </div>
            <button
                type="button"
                onclick="document.getElementById('menuBantuan').classList.add('hidden')"
                class="rounded-lg px-2 py-1 text-xl text-slate-400 hover:bg-slate-100">
                &times;
            </button>
        </div>

        <div class="space-y-5 text-sm">

            {{-- UMUM --}}
            <div>
                <h4 class="mb-2 font-bold text-blue-700">01. Informasi Umum</h4>
                <p class="leading-relaxed text-slate-500">
                    SIPALA merupakan Sistem Peminjaman Alat SMK Negeri 5 Surakarta
                    yang membantu proses pengelolaan peminjaman dan pengembalian alat sekolah.
                </p>
                <p class="mt-2 font-semibold text-slate-700">Pengguna SIPALA:</p>
                <ul class="mt-1 list-inside list-disc space-y-1 text-slate-500">
                    <li>Admin: Mengelola seluruh data dan transaksi.</li>
                    <li>Guru: Mengajukan peminjaman alat.</li>
                    <li>Siswa: Mengajukan peminjaman alat.</li>
                </ul>
            </div>

            {{-- LOGIN --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">02. Login dan Registrasi</h4>
                <p class="text-slate-500">Untuk masuk ke SIPALA:</p>
                <ol class="mt-2 list-inside list-decimal space-y-1 text-slate-500">
                    <li>Pilih jenis akun.</li>
                    <li>Masukkan username dan password.</li>
                    <li>Klik tombol Masuk.</li>
                </ol>
                <p class="mt-2 text-slate-500">
                    Siswa dan Guru dapat membuat akun melalui menu Registrasi.
                    Admin menggunakan akun yang telah disediakan.
                </p>
            </div>

            {{-- DASHBOARD --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">03. Dashboard</h4>
                <p class="text-slate-500">
                    Dashboard menampilkan ringkasan aktivitas SIPALA,
                    seperti jumlah alat, stok tersedia, peminjaman aktif,
                    pengajuan menunggu, dan aktivitas terbaru.
                </p>
            </div>

            {{-- DATA ALAT --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">04. Pengelolaan Data Alat</h4>
                <p class="text-slate-500">Khusus Admin:</p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-slate-500">
                    <li>Melihat daftar alat.</li>
                    <li>Menambahkan alat baru.</li>
                    <li>Mengubah informasi alat.</li>
                    <li>Mengelola jumlah stok alat.</li>
                    <li>Menghapus data alat.</li>
                </ul>
            </div>

            {{-- KATEGORI --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">05. Kategori Alat</h4>
                <p class="text-slate-500">
                    Admin dapat menambahkan, mengubah, dan menghapus kategori
                    untuk mengelompokkan alat berdasarkan jenisnya.
                </p>
            </div>

            {{-- PENGGUNA --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">06. Data Pengguna</h4>
                <p class="text-slate-500">
                    Admin dapat melihat dan mengelola data akun pengguna,
                    termasuk informasi Siswa, Guru, dan Admin.
                </p>
            </div>

            {{-- PEMINJAMAN --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">07. Pengajuan Peminjaman</h4>
                <p class="text-slate-500">Untuk Siswa dan Guru:</p>
                <ol class="mt-2 list-inside list-decimal space-y-1 text-slate-500">
                    <li>Buka menu Peminjaman.</li>
                    <li>Pilih alat yang dibutuhkan.</li>
                    <li>Masukkan jumlah alat.</li>
                    <li>Tentukan tanggal peminjaman dan pengembalian.</li>
                    <li>Isi keperluan peminjaman.</li>
                    <li>Kirim pengajuan dan tunggu persetujuan Admin.</li>
                </ol>
            </div>

            {{-- PERSETUJUAN --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">08. Persetujuan Peminjaman</h4>
                <p class="text-slate-500">Khusus Admin:</p>
                <ol class="mt-2 list-inside list-decimal space-y-1 text-slate-500">
                    <li>Buka menu Persetujuan.</li>
                    <li>Pilih pengajuan yang menunggu.</li>
                    <li>Periksa data peminjaman.</li>
                    <li>Pilih Setujui atau Tolak.</li>
                    <li>Berikan catatan jika diperlukan.</li>
                </ol>
            </div>

            {{-- PENYERAHAN --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">09. Penyerahan Alat</h4>
                <p class="text-slate-500">
                    Setelah pengajuan disetujui, Admin atau petugas menyiapkan
                    alat sesuai pengajuan dan mencatat penyerahan kepada peminjam.
                </p>
            </div>

            {{-- PENGEMBALIAN --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">10. Pengembalian Alat</h4>
                <p class="text-slate-500">Untuk Siswa dan Guru:</p>
                <ol class="mt-2 list-inside list-decimal space-y-1 text-slate-500">
                    <li>Buka menu Pengembalian.</li>
                    <li>Pilih transaksi peminjaman.</li>
                    <li>Isi jumlah dan kondisi alat.</li>
                    <li>Unggah bukti pengembalian.</li>
                    <li>Kirim pengajuan pengembalian.</li>
                    <li>Tunggu pemeriksaan Admin.</li>
                </ol>
            </div>

            {{-- VERIFIKASI --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">11. Pemeriksaan Pengembalian</h4>
                <p class="text-slate-500">Khusus Admin:</p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-slate-500">
                    <li>Memeriksa bukti foto atau video.</li>
                    <li>Memeriksa jumlah alat.</li>
                    <li>Memeriksa kondisi alat.</li>
                    <li>Mengonfirmasi atau menolak pengembalian.</li>
                </ul>
            </div>

            {{-- RIWAYAT --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">12. Riwayat Transaksi</h4>
                <p class="text-slate-500">
                    Menampilkan catatan peminjaman dan pengembalian,
                    termasuk kode transaksi, nama peminjam, daftar alat,
                    tanggal, dan status transaksi.
                </p>
            </div>

            {{-- NOTIFIKASI --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">13. Notifikasi</h4>
                <p class="text-slate-500">
                    Notifikasi memberikan informasi mengenai persetujuan,
                    penolakan, pengingat pengembalian, dan keterlambatan.
                    Klik notifikasi untuk membuka detail peminjaman.
                </p>
            </div>

            {{-- LAPORAN --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">14. Laporan</h4>
                <p class="text-slate-500">
                    Admin dapat melihat rekap data peminjaman dan pengembalian.
                    Fitur cetak atau ekspor digunakan jika tersedia pada halaman laporan.
                </p>
            </div>

            {{-- PROFIL --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">15. Profil Pengguna</h4>
                <p class="text-slate-500">
                    Pengguna dapat melihat informasi akun seperti nama,
                    username, peran, kelas atau jabatan, dan nomor HP jika tersedia.
                </p>
            </div>

            {{-- LOGOUT --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-2 font-bold text-blue-700">16. Keluar dari Akun</h4>
                <p class="text-slate-500">
                    Gunakan tombol Logout pada menu akun untuk keluar dari
                    SIPALA setelah selesai menggunakan website.
                </p>
            </div>

            {{-- FAQ --}}
            <div class="border-t border-slate-100 pt-4">
                <h4 class="mb-3 font-bold text-blue-700">17. Pertanyaan Umum (FAQ)</h4>

                <div class="space-y-3">
                    <div>
                        <p class="font-semibold text-slate-700">Mengapa peminjaman belum disetujui?</p>
                        <p class="mt-1 text-slate-500">Pengajuan masih menunggu pemeriksaan Admin.</p>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-700">Mengapa alat tidak tersedia?</p>
                        <p class="mt-1 text-slate-500">Stok alat mungkin habis atau tidak mencukupi.</p>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-700">Bagaimana jika peminjaman ditolak?</p>
                        <p class="mt-1 text-slate-500">Periksa catatan Admin untuk mengetahui alasannya.</p>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-700">Apakah bukti pengembalian wajib?</p>
                        <p class="mt-1 text-slate-500">Ya, bukti perlu diunggah sesuai ketentuan pengembalian.</p>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-700">Bagaimana jika alat rusak atau hilang?</p>
                        <p class="mt-1 text-slate-500">Laporkan kondisi sebenarnya saat mengisi pengembalian.</p>
                    </div>
                </div>
            </div>

            {{-- KONTAK --}}
            <div class="rounded-xl bg-blue-50 p-4">
                <h4 class="font-bold text-blue-800">18. Butuh Bantuan?</h4>
                <p class="mt-2 leading-relaxed text-blue-700">
                    Jika mengalami kendala saat menggunakan SIPALA,
                    silakan hubungi Admin atau petugas pengelola
                    di SMK Negeri 5 Surakarta.
                </p>
            </div>

        </div>

        {{-- FOOTER --}}
        <button
            type="button"
            onclick="document.getElementById('menuBantuan').classList.add('hidden')"
            class="mt-5 w-full rounded-xl bg-blue-600 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
            Mengerti
        </button>
    </div>
</div>
        </div>
    </div>
</div>
</header>


{{-- TOMBOL AJUKAN --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <p class="text-sm font-bold text-white">Ringkasan Peminjaman</p>
        <p class="mt-1 text-xs text-blue-100/70">Informasi aktivitas peminjaman alat kamu.</p>
    </div>

    <a href="{{ route('peminjaman.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
        <span class="text-lg">+</span> Ajukan Peminjaman
    </a>
</div>

{{-- KARTU STATISTIK --}}
<section class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

    <article class="rounded-xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:shadow-xl">
        <div class="flex items-center justify-between gap-2">
            <p class="text-xs font-semibold text-slate-700">Peminjaman Pending</p>
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-lg text-amber-600">◷</span>
        </div>
        <p class="angka mt-3 text-3xl font-extrabold text-slate-900">{{ $menunggu }} <span class="text-sm font-medium text-slate-500">Alat</span></p>
        <p class="mt-2 text-[11px] text-slate-500">Menunggu persetujuan admin</p>
    </article>

    <article class="rounded-xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:shadow-xl">
        <div class="flex items-center justify-between gap-2">
            <p class="text-xs font-semibold text-slate-700">Peminjaman Aktif</p>
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100 text-lg text-blue-600">✓</span>
        </div>
        <p class="angka mt-3 text-3xl font-extrabold text-slate-900">{{ $sedangDipinjam }} <span class="text-sm font-medium text-slate-500">Alat</span></p>
        <p class="mt-2 text-[11px] text-slate-500">Disetujui atau sedang dipinjam</p>
    </article>

    <article class="rounded-xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:shadow-xl">
        <div class="flex items-center justify-between gap-2">
            <p class="text-xs font-semibold text-slate-700">Riwayat Selesai</p>
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-100 text-lg text-emerald-600">◇</span>
        </div>
        <p class="angka mt-3 text-3xl font-extrabold text-slate-900">{{ $selesai }} <span class="text-sm font-medium text-slate-500">Kali</span></p>
        <p class="mt-2 text-[11px] text-slate-500">Peminjaman telah dikembalikan</p>
    </article>

    <article class="rounded-xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10 transition hover:-translate-y-0.5 hover:shadow-xl">
        <div class="flex items-center justify-between gap-2">
            <p class="text-xs font-semibold text-slate-700">Alat Terlambat</p>
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-100 text-lg text-red-600">!</span>
        </div>
        <p class="angka mt-3 text-3xl font-extrabold text-slate-900">{{ $terlambat }} <span class="text-sm font-medium text-slate-500">Alat</span></p>
        <p class="mt-2 text-[11px] text-slate-500">Perlu segera dikembalikan</p>
    </article>

</section>

{{-- AKTIVITAS DAN ALAT POPULER --}}
<section class="grid grid-cols-1 items-start gap-4 xl:grid-cols-5">

    {{-- AKTIVITAS TERBARU --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10 xl:col-span-3">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-sm font-bold text-slate-900">Aktivitas Peminjaman Terbaru</h2>
            <a href="{{ route('peminjaman.index') }}"
               class="text-xs font-semibold text-blue-600 hover:text-blue-800">Lihat semua →</a>
        </div>

        @forelse ($aktivitas as $p)
            <a href="{{ route('peminjaman.show', $p) }}"
               class="flex gap-3 border-b border-slate-100 py-4 first:pt-1 last:border-0 last:pb-1">

                <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full
                    {{ $p->status === 'ditolak' || $p->status === 'dibatalkan' ? 'bg-red-500' : ($p->status === 'menunggu' ? 'bg-amber-400' : 'bg-teal-600') }}">
                </span>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <p class="text-xs font-semibold text-slate-900">
                            {{ $p->keperluan ?: $p->kode }}
                        </p>
                        @include('komponen-status', ['status' => $p->status])
                    </div>

                    <p class="mt-1 text-[11px] leading-5 text-slate-500">
                        {{ $p->kode }} · {{ $p->created_at->format('d/m/Y') }}
                    </p>

                    <p class="mt-1 text-[11px] text-slate-600">
                        @foreach ($p->items as $item)
                            {{ $item->alat->nama ?? 'Alat' }} ×{{ $item->jumlah }}@if (!$loop->last), @endif
                        @endforeach
                    </p>
                </div>
            </a>
        @empty
            <div class="rounded-lg bg-slate-50 px-4 py-8 text-center">
                <div class="mb-2 text-3xl">▤</div>
                <p class="text-sm font-semibold text-slate-900">Belum ada aktivitas peminjaman</p>
                <p class="mt-1 text-xs text-slate-500">Ajukan peminjaman alat untuk mulai menggunakan SIPALA.</p>
                <a href="{{ route('alat.index') }}"
                   class="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                    Lihat Daftar Alat
                </a>
            </div>
        @endforelse
    </div>

    {{-- ALAT POPULER --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 text-slate-900 shadow-lg shadow-black/10 xl:col-span-2">
        <div class="mb-4">
            <h2 class="text-sm font-bold text-slate-900">Alat Populer & Sering Dipinjam</h2>
            <p class="mt-1 text-[11px] text-slate-500">Pilihan alat di katalog SIPALA</p>
        </div>

        @forelse ($alatPopuler as $alat)
            <a href="{{ route('alat.show', $alat) }}"
               class="flex items-center gap-3 border-b border-slate-100 py-3 first:pt-1 last:border-0 last:pb-1">

                <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-slate-100 text-xl">
                    @if ($alat->gambar ?? false)
                        <img src="{{ asset('storage/'.$alat->gambar) }}"
                             alt="{{ $alat->nama }}"
                             class="h-full w-full object-cover">
                    @else
                        <span>▧</span>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold leading-5 text-slate-900">
                        {{ $alat->nama }}
                    </p>
                    <p class="mt-1 text-[10px] text-slate-500">
                        Kode: {{ $alat->kode }}
                    </p>
                    <p class="mt-1 text-[10px] font-medium text-teal-700">
                        {{ $alat->stok_tersedia }} dari {{ $alat->stok_total }} tersedia
                    </p>
                </div>
            </a>
        @empty
            <div class="rounded-lg bg-slate-50 px-4 py-8 text-center">
                <p class="text-sm font-semibold text-slate-900">Belum ada data alat</p>
                <p class="mt-1 text-xs text-slate-500">Alat akan muncul setelah tersedia di katalog.</p>
            </div>
        @endforelse

        <a href="{{ route('alat.index') }}"
           class="mt-4 block rounded-lg border border-slate-200 px-4 py-2.5 text-center text-xs font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
            Lihat Semua Alat
        </a>
    </div>

</section>
@endsection
