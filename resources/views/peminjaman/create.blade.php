@extends('layouts.app')
@section('judul', 'Ajukan Peminjaman')

@section('konten')
<div class="space-y-6">

    {{-- Header --}}
    <header>
        <a href="{{ route('peminjaman.index') }}"
           class="text-sm font-semibold text-blue-200 transition hover:text-white">
            ← Kembali ke peminjaman
        </a>

        <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-white">
            Ajukan Peminjaman
        </h1>
        <p class="mt-2 text-sm text-blue-100/80 sm:text-base">
            Pilih alat, tentukan tanggal, lalu tunggu petugas menyetujui pengajuanmu.
        </p>
    </header>

    {{-- Pesan error --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="mb-2 font-bold">Pengajuan belum bisa dikirim.</p>
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form utama --}}
    <form method="POST"
          action="{{ route('peminjaman.store') }}"
          x-data="{
              items: [{
                  alat_id: '{{ old('items.0.alat_id', $pilihan) }}',
                  jumlah: '{{ old('items.0.jumlah', 1) }}'
              }]
          }"
          class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">

        @csrf

        {{-- Judul kartu --}}
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-5 sm:px-7">
            <h2 class="text-lg font-extrabold text-slate-900">
                Detail Peminjaman
            </h2>
            <p class="mt-1 text-sm text-slate-600">
                Isi semua data berikut dengan benar.
            </p>
        </div>

        <div class="space-y-7 p-5 sm:p-7">

            {{-- Tanggal --}}
            <div class="grid gap-5 sm:grid-cols-2">

                <div>
                    <label for="tanggal_pinjam"
                           class="mb-2 block text-sm font-bold text-slate-800">
                        Tanggal Pinjam <span class="text-red-600">*</span>
                    </label>

                    <input id="tanggal_pinjam"
                           type="date"
                           name="tanggal_pinjam"
                           value="{{ old('tanggal_pinjam', now()->format('Y-m-d')) }}"
                           min="{{ now()->format('Y-m-d') }}"
                           required
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100">

                    @error('tanggal_pinjam')
                        <p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_jatuh_tempo"
                           class="mb-2 block text-sm font-bold text-slate-800">
                        Rencana Pengembalian <span class="text-red-600">*</span>
                    </label>

                    <input id="tanggal_jatuh_tempo"
                           type="date"
                           name="tanggal_jatuh_tempo"
                           value="{{ old('tanggal_jatuh_tempo', now()->addDays(3)->format('Y-m-d')) }}"
                           min="{{ now()->format('Y-m-d') }}"
                           required
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100">

                    @error('tanggal_jatuh_tempo')
                        <p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Keperluan --}}
            <div>
                <label for="keperluan"
                       class="mb-2 block text-sm font-bold text-slate-800">
                    Keperluan Peminjaman <span class="text-red-600">*</span>
                </label>

                <textarea id="keperluan"
                          name="keperluan"
                          rows="4"
                          required
                          placeholder="Contoh: Praktik instalasi jaringan kelas XII"
                          class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-600 focus:ring-4 focus:ring-blue-100">{{ old('keperluan') }}</textarea>

                @error('keperluan')
                    <p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pilihan alat --}}
            <div>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900">
                            Alat yang Dipinjam
                        </h2>
                        <p class="mt-1 text-sm text-slate-600">
                            Pilih alat dan masukkan jumlah unit yang dibutuhkan.
                        </p>
                    </div>

                    <button type="button"
                            @click="items.push({alat_id: '', jumlah: 1})"
                            class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-bold text-blue-800 transition hover:bg-blue-100">
                        + Tambah Alat
                    </button>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="mb-4 grid grid-cols-1 gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-12">

                        <div class="sm:col-span-8">
                            <label class="mb-2 block text-sm font-bold text-slate-800">
                                Pilih Alat <span class="text-red-600">*</span>
                            </label>

                            <select :name="'items[' + index + '][alat_id]'"
                                    x-model="item.alat_id"
                                    required
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900 outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100">

                                <option value="">-- Pilih alat --</option>

                                @foreach ($alats as $alat)
                                    <option value="{{ $alat->id }}"
                                            @if ($alat->stok_tersedia < 1) disabled @endif>
                                        {{ $alat->kode }} - {{ $alat->nama }}
                                        (tersedia: {{ $alat->stok_tersedia }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-bold text-slate-800">
                                Jumlah <span class="text-red-600">*</span>
                            </label>

                            <input type="number"
                                   :name="'items[' + index + '][jumlah]'"
                                   x-model="item.jumlah"
                                   min="1"
                                   step="1"
                                   required
                                   class="w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900 outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100">
                        </div>

                        <div class="flex items-end sm:col-span-2">
                            <button type="button"
                                    x-show="items.length > 1"
                                    @click="items.splice(index, 1)"
                                    class="w-full rounded-lg border border-red-200 bg-red-50 px-3 py-3 text-sm font-bold text-red-700 transition hover:bg-red-100">
                                Hapus Baris
                            </button>
                        </div>

                    </div>
                </template>

                @error('items')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror

                @error('items.*.alat_id')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror

                @error('items.*.jumlah')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pengingat --}}
            <div class="flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="h-5 w-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z" />
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="m9 12 2 2 4-4" />
                    </svg>
                </div>

                <div>
                    <p class="text-sm font-bold text-blue-900">
                        Ingat sebelum mengirim
                    </p>
                    <p class="mt-1 text-sm leading-relaxed text-blue-800">
                        Pastikan data sudah benar. Jaga alat yang dipinjam dan kembalikan sesuai tanggal yang ditentukan.
                    </p>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex flex-wrap gap-3 border-t border-slate-200 pt-6">

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-6 py-3 text-sm font-extrabold text-white shadow-md transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">
                    Kirim Pengajuan
                </button>

                <a href="{{ route('peminjaman.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100">
                    Batal
                </a>

            </div>
        </div>
    </form>
</div>
@endsection
