@extends('layouts.app')

@section('judul', 'Detail '.$peminjaman->kode)

@section('konten')
@php
    $pengajuanKembali = $peminjaman->pengembalian;
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
@endphp

<div class="space-y-6 text-slate-900">

    <a href="{{ route('peminjaman.index') }}"
       class="text-sm font-semibold text-slate-700 hover:text-blue-700">
        ← Kembali ke daftar
    </a>

    {{-- DETAIL PEMINJAMAN --}}
    <article class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-dashed border-slate-200 p-6">
            <div>
                <p class="text-sm font-medium text-slate-600">{{ $peminjaman->kode }}</p>

                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">
                    {{ $peminjaman->keperluan }}
                </h1>

                <p class="mt-1 text-slate-700">
                    {{ $peminjaman->user->name }}
                    @if ($peminjaman->user->kelas_jabatan)
                        · {{ $peminjaman->user->kelas_jabatan }}
                    @endif
                    @if ($peminjaman->user->username)
                        · {{ $peminjaman->user->username }}
                    @endif
                </p>
            </div>

            <div class="text-right">
                @include('komponen-status', ['status' => $peminjaman->status])

                @if ($peminjaman->isTerlambat())
                    <p class="mt-1 text-sm font-semibold text-red-700">
                        Telat {{ $peminjaman->hariTerlambat() }} hari
                    </p>
                @endif
            </div>
        </div>

        <dl class="grid gap-4 border-b border-dashed border-slate-200 p-6 sm:grid-cols-3">
            @foreach ([
                'Tanggal pinjam' => $peminjaman->tanggal_pinjam->translatedFormat('j F Y'),
                'Jatuh tempo' => $peminjaman->tanggal_jatuh_tempo->translatedFormat('j F Y'),
                'Dikembalikan' => $peminjaman->tanggal_kembali?->translatedFormat('j F Y') ?? 'Belum',
            ] as $label => $nilai)
                <div>
                    <dt class="text-sm font-medium text-slate-600">{{ $label }}</dt>
                    <dd class="mt-0.5 font-semibold text-slate-900">{{ $nilai }}</dd>
                </div>
            @endforeach
        </dl>

        <div class="p-6">
            <h2 class="mb-3 font-bold text-slate-900">Rincian alat</h2>

            <ul class="divide-y divide-slate-200 rounded-xl border border-slate-200">
                @foreach ($peminjaman->items as $item)
                    <li class="flex flex-wrap items-center justify-between gap-3 px-4 py-4">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $item->alat->nama }}</p>
                            <p class="text-sm text-slate-600">{{ $item->alat->kode }}</p>

                            @if ($item->catatan)
                                <p class="mt-1 text-sm text-slate-700">{{ $item->catatan }}</p>
                            @endif
                        </div>

                        <div class="text-sm text-slate-800">
                            <span class="font-semibold">{{ $item->jumlah }} unit</span>

                            @if ($item->kondisi_kembali)
                                <span class="text-slate-600">
                                    · Kembali {{ $item->jumlah_kembali }}
                                    ({{ $item->kondisi_kembali }})
                                </span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            @if ($peminjaman->catatan_petugas)
                <div class="mt-5 rounded-xl bg-slate-100 p-4">
                    <p class="text-sm text-slate-600">
                        Catatan petugas
                        @if ($peminjaman->petugas)
                            — {{ $peminjaman->petugas->name }}
                        @endif
                    </p>
                    <p class="mt-1 text-slate-900">{{ $peminjaman->catatan_petugas }}</p>
                </div>
            @endif
        </div>
    </article>

    {{-- NOTIFIKASI --}}
    @if (session('sukses'))
        <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            {{ session('sukses') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="mb-2 font-bold">Ada data yang perlu diperbaiki:</p>
            <ul class="list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- PEMBATALAN PEMINJAMAN --}}
    @if (auth()->check() && !$isAdmin && $peminjaman->bisaDibatalkan())
        <form method="POST"
              action="{{ route('peminjaman.batal', $peminjaman) }}"
              onsubmit="return confirm('Batalkan pengajuan ini?')">
            @csrf
            @method('PATCH')

            <button type="submit"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-semibold text-red-700 hover:bg-red-50">
                Batalkan pengajuan
            </button>
        </form>
    @endif

    {{-- TINDAKAN ADMIN --}}
    @if ($isAdmin)
        <section class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-900 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-slate-900">Tindakan petugas</h2>

            @if ($peminjaman->status === 'menunggu')
                <form method="POST"
                      action="{{ route('persetujuan.setujui', $peminjaman) }}"
                      class="mb-4">
                    @csrf
                    @method('PATCH')

                    <label class="mb-2 block text-sm font-semibold text-slate-900">
                        Catatan untuk peminjam (opsional)
                    </label>

                    <input name="catatan_petugas"
                           placeholder="Tambahkan catatan..."
                           class="mb-3 w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:outline-none">

                    <button type="submit"
                            class="rounded-lg bg-teal-700 px-4 py-2.5 font-semibold text-white hover:bg-teal-800">
                        Setujui pengajuan
                    </button>
                </form>

                <form method="POST" action="{{ route('persetujuan.tolak', $peminjaman) }}">
                    @csrf
                    @method('PATCH')

                    <label class="mb-2 block text-sm font-semibold text-slate-900">
                        Alasan penolakan
                    </label>

                    <input name="catatan_petugas"
                           required
                           placeholder="Tuliskan alasan penolakan..."
                           class="mb-3 w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:outline-none">

                    <button type="submit"
                            class="rounded-lg border border-red-300 bg-white px-4 py-2.5 font-semibold text-red-700 hover:bg-red-50">
                        Tolak pengajuan
                    </button>
                </form>

            @elseif ($peminjaman->status === 'disetujui')
                <p class="mb-3 text-sm text-slate-700">
                    Tandai setelah alat diserahkan kepada peminjam.
                </p>

                <form method="POST" action="{{ route('persetujuan.serahkan', $peminjaman) }}">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                            class="rounded-lg bg-blue-700 px-4 py-2.5 font-semibold text-white hover:bg-blue-800">
                        Alat diserahkan
                    </button>
                </form>

            @elseif ($peminjaman->status === 'dipinjam')
                @if ($pengajuanKembali)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="font-bold text-slate-900">Pengajuan bukti pengembalian</p>

                        <p class="mt-2 text-sm text-slate-800">
                            Status: <strong>{{ ucfirst($pengajuanKembali->status) }}</strong>
                        </p>

                        @if ($pengajuanKembali->catatan)
                            <p class="mt-3 text-sm text-slate-800">
                                Catatan peminjam: {{ $pengajuanKembali->catatan }}
                            </p>
                        @endif

                        @if ($pengajuanKembali->bukti_path)
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($pengajuanKembali->bukti_path) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="mt-3 inline-block font-semibold text-blue-700 underline">
                                Lihat bukti pengembalian
                            </a>
                        @endif

                        <div class="mt-5 rounded-xl bg-slate-50 p-4">
                            <h3 class="font-bold text-slate-900">Pemeriksaan kondisi barang</h3>

                            @foreach ($peminjaman->items as $item)
                                @php
                                    $rincian = $pengajuanKembali->detail_items ?? [];
                                    $detail = $rincian[$item->id] ?? [];
                                    $kondisi = $detail['kondisi'] ?? null;

                                    if (!$kondisi) {
                                        $baik = (int) ($detail['jumlah_baik'] ?? 0);
                                        $rusak = (int) ($detail['jumlah_rusak'] ?? 0);
                                        $hilang = (int) ($detail['jumlah_hilang'] ?? 0);

                                        $kondisi = $baik === $item->jumlah
                                            ? 'baik'
                                            : ($rusak === $item->jumlah
                                                ? 'rusak'
                                                : ($hilang === $item->jumlah
                                                    ? 'hilang'
                                                    : 'campuran'));
                                    }
                                @endphp

                                <div class="mt-3 rounded-lg border border-slate-200 bg-white p-3">
                                    <p class="font-semibold text-slate-900">{{ $item->alat->nama }}</p>
                                    <p class="mt-1 text-sm text-slate-800">
                                        Jumlah: {{ $item->jumlah }} unit
                                    </p>
                                    <p class="mt-1 text-sm text-slate-800">
                                        Kondisi dilaporkan:
                                        <strong>{{ ucfirst($kondisi) }}</strong>
                                    </p>

                                    @if (!empty($detail['catatan']))
                                        <p class="mt-1 text-sm text-slate-700">
                                            Catatan: {{ $detail['catatan'] }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if ($pengajuanKembali->catatan_petugas)
                            <p class="mt-3 text-sm text-slate-800">
                                Catatan petugas: {{ $pengajuanKembali->catatan_petugas }}
                            </p>
                        @endif
                    </div>

                    @if ($pengajuanKembali->status === 'menunggu')
                        <form method="POST"
                              action="{{ route('pengembalian.konfirmasi', $pengajuanKembali) }}"
                              class="mt-4 rounded-xl border border-green-200 p-4"
                              onsubmit="return confirm('Konfirmasi pengembalian ini? Stok barang akan diperbarui sesuai kondisi.')">
                            @csrf
                            @method('PATCH')

                            <label class="mb-2 block text-sm font-semibold text-slate-900">
                                Catatan petugas (opsional)
                            </label>

                            <textarea name="catatan_petugas"
                                      rows="2"
                                      maxlength="1000"
                                      placeholder="Catatan hasil pemeriksaan..."
                                      class="mb-3 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:outline-none"></textarea>

                            <button type="submit"
                                    class="w-full rounded-xl bg-teal-700 px-4 py-3 font-semibold text-white hover:bg-teal-800">
                                Konfirmasi Pengembalian
                            </button>
                        </form>

                        <form method="POST"
                              action="{{ route('pengembalian.tolak', $pengajuanKembali) }}"
                              class="mt-4 rounded-xl border border-red-200 p-4"
                              onsubmit="return confirm('Tolak pengajuan pengembalian ini?')">
                            @csrf
                            @method('PATCH')

                            <label class="mb-2 block text-sm font-semibold text-slate-900">
                                Alasan penolakan <span class="text-red-700">*</span>
                            </label>

                            <textarea name="catatan_petugas"
                                      rows="3"
                                      maxlength="1000"
                                      required
                                      placeholder="Tuliskan alasan penolakan..."
                                      class="mb-3 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-slate-900 placeholder:text-slate-500 focus:border-blue-600 focus:outline-none"></textarea>

                            <button type="submit"
                                    class="w-full rounded-xl border border-red-300 bg-white px-4 py-3 font-semibold text-red-700 hover:bg-red-50">
                                Tolak Pengembalian
                            </button>
                        </form>

                    @elseif ($pengajuanKembali->status === 'dikonfirmasi')
                        <div class="mt-4 rounded-xl bg-green-50 p-4 text-sm font-semibold text-green-800">
                            Pengembalian sudah dikonfirmasi.
                        </div>

                    @elseif ($pengajuanKembali->status === 'ditolak')
                        <div class="mt-4 rounded-xl bg-red-50 p-4 text-sm text-red-800">
                            <p class="font-bold">Pengembalian ditolak.</p>
                            <p class="mt-1">
                                Peminjam dapat mengajukan kembali setelah memperbaiki data.
                            </p>
                        </div>
                    @endif
                @else
                    <p class="text-sm text-slate-700">
                        Belum ada pengajuan bukti pengembalian dari peminjam.
                    </p>
                @endif

            @else
                <p class="text-sm text-slate-700">
                    Peminjaman ini sudah selesai diproses.
                </p>
            @endif
        </section>
    @endif

</div>
@endsection
