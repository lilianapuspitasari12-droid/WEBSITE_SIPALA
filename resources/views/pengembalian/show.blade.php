@extends('layouts.app')
@section('judul', 'Detail Pengembalian')

@section('konten')
@php
    $isAdmin = auth()->user()->isAdmin();
    $pengajuan = $peminjaman->pengembalian;

    $bolehMengajukan = !$isAdmin
        && $peminjaman->status === 'dipinjam'
        && (!$pengajuan || $pengajuan->status === 'ditolak');
@endphp

<div class="mx-auto max-w-5xl space-y-6">

    {{-- HEADER --}}
    <header class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-700 via-blue-600 to-teal-500 p-6 text-white shadow-xl sm:p-8">
        <div class="absolute -right-8 -top-8 h-40 w-40 rounded-full bg-white/10"></div>

        <div class="relative">
            <a href="{{ route('pengembalian.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-blue-100 hover:text-white">
                ← Kembali ke Pengembalian
            </a>

            <p class="mb-2 mt-5 text-xs font-semibold uppercase tracking-[0.2em] text-blue-100">
                SIPALA / Pengembalian
            </p>

            <h1 class="text-2xl font-extrabold sm:text-3xl">
                Detail Pengembalian Alat
            </h1>

            <p class="mt-2 text-sm leading-6 text-blue-50">
                Periksa informasi peminjaman dan lengkapi form pengembalian alat.
            </p>
        </div>
    </header>

    {{-- NOTIFIKASI --}}
    @if(session('sukses'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800">
            ✓ {{ session('sukses') }}
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="mb-2 font-bold">Periksa kembali data berikut:</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- INFORMASI PEMINJAMAN --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">
                    Kode Peminjaman
                </p>
                <h2 class="mt-1 text-2xl font-extrabold text-blue-800">
                    {{ $peminjaman->kode }}
                </h2>
            </div>

            @if($pengajuan)
                <span class="w-fit rounded-full px-4 py-2 text-xs font-bold
                    {{ $pengajuan->status === 'menunggu'
                        ? 'bg-amber-100 text-amber-800'
                        : ($pengajuan->status === 'dikonfirmasi'
                            ? 'bg-emerald-100 text-emerald-800'
                            : 'bg-red-100 text-red-800') }}">
                    {{ $pengajuan->status === 'menunggu'
                        ? 'Menunggu Pemeriksaan'
                        : ($pengajuan->status === 'dikonfirmasi'
                            ? 'Sudah Dikonfirmasi'
                            : 'Pengajuan Ditolak') }}
                </span>
            @else
                <span class="w-fit rounded-full bg-blue-100 px-4 py-2 text-xs font-bold text-blue-800">
                    Sedang Dipinjam
                </span>
            @endif
        </div>

        <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
            @if($isAdmin)
                <div class="rounded-xl bg-slate-50 p-4 sm:col-span-2">
                    <p class="text-xs font-semibold text-slate-500">Nama Peminjam</p>
                    <p class="mt-1 font-bold text-slate-800">
                        {{ $peminjaman->user->name ?? '-' }}
                    </p>
                </div>
            @endif

            <div class="rounded-xl bg-slate-50 p-4">
                <p class="text-xs font-semibold text-slate-500">Tanggal Pinjam</p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ $peminjaman->tanggal_pinjam
                        ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y')
                        : '-' }}
                </p>
            </div>

            <div class="rounded-xl bg-slate-50 p-4">
                <p class="text-xs font-semibold text-slate-500">Batas Pengembalian</p>
                <p class="mt-1 font-bold text-slate-800">
                    {{ $peminjaman->tanggal_jatuh_tempo
                        ? \Illuminate\Support\Carbon::parse($peminjaman->tanggal_jatuh_tempo)->format('d M Y')
                        : '-' }}
                </p>
            </div>
        </div>
    </section>

    {{-- DAFTAR ALAT --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <h2 class="text-lg font-extrabold text-slate-800">
            Daftar Alat yang Dipinjam
        </h2>

        <div class="mt-4 space-y-3">
            @forelse($peminjaman->items as $item)
                <div class="rounded-xl border border-slate-200 p-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-bold text-slate-800">
                                {{ $item->alat->nama ?? 'Alat tidak tersedia' }}
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Kode alat: {{ $item->alat->kode ?? '-' }}
                            </p>
                        </div>

                        <span class="w-fit rounded-lg bg-blue-50 px-3 py-2 text-sm font-bold text-blue-800">
                            Jumlah: {{ $item->jumlah }}
                        </span>
                    </div>

                    @if($pengajuan && is_array($pengajuan->detail_items))
                        @php
                            $detailItem = $pengajuan->detail_items[$item->id] ?? [];
                        @endphp

                        @if(isset($detailItem['kondisi']))
                            <p class="mt-3 text-sm text-slate-600">
                                Kondisi dilaporkan:
                                <strong>{{ ucfirst($detailItem['kondisi']) }}</strong>
                            </p>
                        @endif

                        @if(!empty($detailItem['catatan']))
                            <p class="mt-1 text-sm text-slate-600">
                                Catatan: {{ $detailItem['catatan'] }}
                            </p>
                        @endif
                    @endif
                </div>
            @empty
                <p class="text-sm text-slate-500">Tidak ada rincian alat.</p>
            @endforelse
        </div>
    </section>

    {{-- FORM PENGAJUAN SISWA --}}
    @if($bolehMengajukan)
        <section class="rounded-2xl border border-teal-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="rounded-xl bg-teal-50 p-4">
                <h2 class="text-xl font-extrabold text-slate-800">
                    Form Pengembalian
                </h2>
                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Isi kondisi setiap alat dan unggah foto atau video bukti pengembalian.
                    Bukti wajib diunggah sebelum form dapat dikirim.
                </p>
            </div>

            @if($pengajuan && $pengajuan->status === 'ditolak' && $pengajuan->catatan_petugas)
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4">
                    <p class="font-bold text-red-800">Alasan pengajuan ditolak</p>
                    <p class="mt-1 whitespace-pre-line text-sm text-red-700">
                        {{ $pengajuan->catatan_petugas }}
                    </p>
                    <p class="mt-2 text-sm text-red-700">
                        Silakan perbaiki data dan kirim pengajuan kembali.
                    </p>
                </div>
            @endif

            <form action="{{ route('pengembalian.store', $peminjaman) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="mt-5 space-y-5">
                @csrf

                {{-- KONDISI PER ALAT --}}
                @foreach($peminjaman->items as $item)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="font-bold text-slate-800">
                            {{ $item->alat->nama ?? 'Alat tidak tersedia' }}
                        </p>
                        <p class="mb-4 mt-1 text-xs text-slate-500">
                            Jumlah dipinjam: {{ $item->jumlah }}
                        </p>

                        <label for="kondisi-{{ $item->id }}"
                               class="mb-2 block text-sm font-bold text-slate-700">
                            Kondisi Alat <span class="text-red-600">*</span>
                        </label>

                        <select id="kondisi-{{ $item->id }}"
                                name="items[{{ $item->id }}][kondisi]"
                                required
                                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100">
                            <option value="">-- Pilih kondisi --</option>
                            <option value="baik" {{ old('items.'.$item->id.'.kondisi') === 'baik' ? 'selected' : '' }}>
                                Baik
                            </option>
                            <option value="rusak" {{ old('items.'.$item->id.'.kondisi') === 'rusak' ? 'selected' : '' }}>
                                Rusak
                            </option>
                            <option value="hilang" {{ old('items.'.$item->id.'.kondisi') === 'hilang' ? 'selected' : '' }}>
                                Hilang
                            </option>
                        </select>

                        <label for="catatan-{{ $item->id }}"
                               class="mb-2 mt-4 block text-sm font-bold text-slate-700">
                            Catatan Kondisi (opsional)
                        </label>

                        <textarea id="catatan-{{ $item->id }}"
                                  name="items[{{ $item->id }}][catatan]"
                                  rows="2"
                                  maxlength="255"
                                  class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                                  placeholder="Contoh: Ada goresan pada bagian luar.">{{ old('items.'.$item->id.'.catatan') }}</textarea>
                    </div>
                @endforeach

                {{-- BUKTI WAJIB --}}
                <div class="rounded-xl border border-teal-200 bg-teal-50/70 p-4">
                    <label for="bukti" class="mb-2 block text-sm font-bold text-slate-800">
                        Foto atau Video Bukti <span class="text-red-600">*</span>
                    </label>

                    <input id="bukti"
                           type="file"
                           name="bukti"
                           required
                           accept=".jpg,.jpeg,.png,.webp,.mp4,.mov,.webm,image/jpeg,image/png,image/webp,video/mp4,video/quicktime,video/webm"
                           class="block w-full rounded-xl border border-slate-300 bg-white p-3 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-bold file:text-blue-700">

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Wajib diisi. Format JPG, JPEG, PNG, WEBP, MP4, MOV, atau WEBM.
                        Ukuran maksimal 20 MB.
                    </p>
                </div>

                {{-- CATATAN UMUM --}}
                <div>
                    <label for="catatan" class="mb-2 block text-sm font-bold text-slate-700">
                        Catatan Pengembalian (opsional)
                    </label>

                    <textarea id="catatan"
                              name="catatan"
                              rows="3"
                              maxlength="1000"
                              class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-100"
                              placeholder="Tambahkan keterangan jika diperlukan.">{{ old('catatan') }}</textarea>
                </div>

                <button type="submit"
                        onclick="return confirm('Kirim pengajuan pengembalian beserta bukti?')"
                        class="w-full rounded-xl bg-gradient-to-r from-blue-700 to-teal-600 px-5 py-4 text-sm font-bold text-white shadow-md transition hover:from-blue-800 hover:to-teal-700">
                    Kirim Pengajuan Pengembalian →
                </button>
            </form>
        </section>

    {{-- PENGAJUAN MASIH DIPERIKSA --}}
    @elseif(!$isAdmin && $pengajuan && $pengajuan->status === 'menunggu')
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 sm:p-6">
            <h2 class="text-lg font-extrabold text-amber-900">
                Pengajuan Sedang Diperiksa
            </h2>
            <p class="mt-2 text-sm leading-6 text-amber-800">
                Bukti pengembalian sudah dikirim. Kamu tidak perlu mengirim ulang.
            </p>
        </section>

    {{-- SUDAH DIKONFIRMASI --}}
    @elseif($pengajuan && $pengajuan->status === 'dikonfirmasi')
        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 sm:p-6">
            <h2 class="text-lg font-extrabold text-emerald-900">
                Pengembalian Sudah Dikonfirmasi
            </h2>
            <p class="mt-2 text-sm text-emerald-800">
                Pengembalian ini telah diproses oleh petugas.
            </p>
        </section>
    @endif

    {{-- BUKTI YANG DIKIRIM: TAMPIL UNTUK SISWA DAN ADMIN --}}
    @if($pengajuan && $pengajuan->bukti_path)
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-extrabold text-slate-800">
                Bukti Pengembalian
            </h2>

            @if($pengajuan->catatan)
                <p class="mt-2 whitespace-pre-line text-sm text-slate-600">
                    Catatan: {{ $pengajuan->catatan }}
                </p>
            @endif

            <div class="mt-4">
                @if(\Illuminate\Support\Str::startsWith($pengajuan->bukti_mime ?? '', 'image/'))
                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($pengajuan->bukti_path) }}"
                       target="_blank"
                       rel="noopener noreferrer">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($pengajuan->bukti_path) }}"
                             alt="Bukti pengembalian"
                             class="max-h-80 w-auto rounded-xl border border-slate-200 object-contain">
                    </a>
                @else
                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($pengajuan->bukti_path) }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex rounded-xl bg-blue-700 px-4 py-3 text-sm font-bold text-white hover:bg-blue-800">
                        ▶ Buka Bukti Video
                    </a>
                @endif
            </div>

            @if($pengajuan->catatan_petugas)
                <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">
                        Catatan Petugas
                    </p>
                    <p class="mt-1 whitespace-pre-line text-sm text-slate-800">
                        {{ $pengajuan->catatan_petugas }}
                    </p>
                </div>
            @endif

            {{-- AKSI ADMIN --}}
            @if($isAdmin && $pengajuan->status === 'menunggu')
                <form action="{{ route('pengembalian.konfirmasi', $pengajuan) }}"
                      method="POST"
                      class="mt-5 space-y-3 rounded-xl border border-emerald-200 p-4">
                    @csrf
                    @method('PATCH')

                    <label class="block text-sm font-bold text-slate-800">
                        Catatan Petugas (opsional)
                    </label>
                    <textarea name="catatan_petugas"
                              rows="2"
                              maxlength="1000"
                              class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                              placeholder="Catatan hasil pemeriksaan."></textarea>

                    <button type="submit"
                            onclick="return confirm('Konfirmasi pengembalian ini?')"
                            class="w-full rounded-xl bg-emerald-600 px-4 py-3 text-sm font-bold text-white hover:bg-emerald-700">
                        ✓ Konfirmasi Pengembalian
                    </button>
                </form>

                <form action="{{ route('pengembalian.tolak', $pengajuan) }}"
                      method="POST"
                      class="mt-4 space-y-3 rounded-xl border border-red-200 p-4">
                    @csrf
                    @method('PATCH')

                    <label class="block text-sm font-bold text-slate-800">
                        Alasan Penolakan <span class="text-red-600">*</span>
                    </label>
                    <textarea name="catatan_petugas"
                              rows="2"
                              maxlength="1000"
                              required
                              class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm"
                              placeholder="Tuliskan alasan penolakan."></textarea>

                    <button type="submit"
                            onclick="return confirm('Tolak pengajuan pengembalian ini?')"
                            class="w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-700">
                        ✕ Tolak Pengembalian
                    </button>
                </form>
            @endif
        </section>
    @endif

</div>
@endsection
