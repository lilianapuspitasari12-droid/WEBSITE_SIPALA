@extends('layouts.app')
@section('judul', $alat->exists ? 'Ubah alat' : 'Tambah alat')

@section('konten')
<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
            {{ $alat->exists ? 'Ubah '.$alat->nama : 'Tambah alat baru' }}
        </h1>
        <p class="mt-2 text-sm text-slate-600">
            Lengkapi informasi inventaris peralatan laboratorium RPL.
        </p>
    </div>

    {{-- PESAN ERROR --}}
    @if ($errors->any())
        <div class="rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-bold">Periksa lagi isian berikut:</p>
            <ul class="mt-1 list-inside list-disc font-medium">
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM ALAT --}}
    <form method="POST"
          enctype="multipart/form-data"
          action="{{ $alat->exists ? route('alat.update', $alat) : route('alat.store') }}"
          class="space-y-5 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">

        @csrf

        @if ($alat->exists)
            @method('PUT')
        @endif

        <div class="grid gap-5 sm:grid-cols-2">

            {{-- KODE --}}
            <div>
                <label for="kode" class="mb-1 block text-sm font-bold text-slate-800">
                    Kode Alat
                </label>
                <input id="kode" name="kode"
                       value="{{ old('kode', $alat->kode) }}"
                       placeholder="Contoh: ALT-001" required
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-900 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            {{-- NAMA --}}
            <div>
                <label for="nama" class="mb-1 block text-sm font-bold text-slate-800">
                    Nama Alat
                </label>
                <input id="nama" name="nama"
                       value="{{ old('nama', $alat->nama) }}"
                       placeholder="Contoh: Laptop Lenovo" required
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-900 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            {{-- KATEGORI --}}
            <div>
                <label for="kategori_id" class="mb-1 block text-sm font-bold text-slate-800">
                    Kategori Alat
                </label>
                <select id="kategori_id" name="kategori_id" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-900 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="">Pilih kategori alat</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->id }}"
                            @selected(old('kategori_id', $alat->kategori_id) == $kategori->id)>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-slate-500">
                    Pilih salah satu kategori inventaris RPL.
                </p>
            </div>

            {{-- MERK --}}
            <div>
                <label for="merk" class="mb-1 block text-sm font-bold text-slate-800">
                    Merk
                </label>
                <input id="merk" name="merk"
                       value="{{ old('merk', $alat->merk) }}"
                       placeholder="Contoh: Lenovo, TP-Link, Canon"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-900 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            {{-- LOKASI --}}
            <div>
                <label for="lokasi" class="mb-1 block text-sm font-bold text-slate-800">
                    Lokasi Penyimpanan
                </label>
                <input id="lokasi" name="lokasi"
                       value="{{ old('lokasi', $alat->lokasi) }}"
                       placeholder="Contoh: Lemari A1, Ruang Lab"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-900 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            {{-- KONDISI --}}
            <div>
                <label for="kondisi" class="mb-1 block text-sm font-bold text-slate-800">
                    Kondisi Alat
                </label>
                <select id="kondisi" name="kondisi"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-900 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    @foreach (\App\Models\Alat::KONDISI as $nilai => $label)
                        <option value="{{ $nilai }}"
                            @selected(old('kondisi', $alat->kondisi) === $nilai)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- STOK --}}
            <div>
                <label for="stok_total" class="mb-1 block text-sm font-bold text-slate-800">
                    Jumlah Stok
                </label>
                <input id="stok_total" type="number" min="1"
                       name="stok_total"
                       value="{{ old('stok_total', $alat->stok_total) }}"
                       required
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-semibold text-slate-900 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100">

                @if ($alat->exists)
                    <p class="mt-1 text-xs text-slate-500">
                        {{ $alat->stok_total - $alat->stok_tersedia }}
                        unit sedang dipinjam.
                    </p>
                @endif
            </div>

            {{-- FOTO --}}
            <div>
                <label for="foto" class="mb-1 block text-sm font-bold text-slate-800">
                    Foto Alat
                </label>
                <input id="foto" type="file" name="foto" accept="image/*"
                       class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-800 file:mr-3 file:rounded file:border-0 file:bg-blue-100 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-blue-900">

                @if ($alat->exists && $alat->foto)
                    <p class="mt-2 text-xs text-slate-500">
                        Foto saat ini:
                    </p>
                    <img src="{{ Storage::url($alat->foto) }}"
                         alt="{{ $alat->nama }}"
                         class="mt-2 h-24 w-24 rounded-lg border border-slate-200 object-cover">
                @endif
            </div>

        </div>

        {{-- DESKRIPSI --}}
        <div>
            <label for="deskripsi" class="mb-1 block text-sm font-bold text-slate-800">
                Deskripsi Alat
            </label>
            <textarea id="deskripsi" name="deskripsi" rows="4"
                      placeholder="Jelaskan fungsi dan kegunaan alat untuk kegiatan praktik RPL..."
                      class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-900 placeholder:text-slate-400 focus:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-100">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
            <p class="mt-1 text-xs text-slate-500">
                Tuliskan fungsi, kegunaan, atau informasi tambahan mengenai alat.
            </p>
        </div>

        {{-- STATUS AKTIF --}}
        <label class="flex items-center gap-2 text-sm font-semibold text-slate-800">
            <input type="checkbox" name="aktif" value="1"
                   @checked(old('aktif', $alat->aktif ?? true))
                   class="rounded border-slate-400 text-blue-800 focus:ring-blue-700">
            Tampilkan di katalog dan boleh dipinjam
        </label>

        {{-- TOMBOL --}}
        <div class="flex flex-wrap gap-2 border-t border-slate-200 pt-5">
            <button type="submit"
                    class="rounded-lg bg-blue-800 px-5 py-2.5 font-bold text-white transition hover:bg-blue-900">
                Simpan
            </button>

            <a href="{{ route('alat.index') }}"
               class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 font-bold text-slate-800 transition hover:bg-slate-100">
                Batal
            </a>
        </div>

    </form>
</div>
@endsection
