<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeminjamanRequest;
use App\Models\Alat;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $peminjamans = Peminjaman::with('user', 'items.alat')
            ->when(! $user->isAdmin(), fn ($q) => $q->milik($user))
            ->status($request->query('status'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function create(Request $request): View
    {
        return view('peminjaman.create', [
            'alats' => Alat::tersedia()->with('kategori')->orderBy('nama')->get(),
            'pilihan' => $request->query('alat'),
        ]);
    }

    public function store(PeminjamanRequest $request): RedirectResponse
    {
        $peminjaman = DB::transaction(function () use ($request) {
            $peminjaman = Peminjaman::create([
                'kode' => Peminjaman::buatKode(),
                'user_id' => $request->user()->id,
                'tanggal_pinjam' => $request->date('tanggal_pinjam'),
                'tanggal_jatuh_tempo' => $request->date('tanggal_jatuh_tempo'),
                'keperluan' => $request->string('keperluan'),
                'status' => 'menunggu',
            ]);

            foreach ($request->validated('items') as $item) {
                // Kunci baris alat supaya stok tidak bentrok saat banyak pengaju bersamaan.
                $alat = Alat::lockForUpdate()->findOrFail($item['alat_id']);

                abort_if($item['jumlah'] > $alat->stok_tersedia, 422, "Stok {$alat->nama} sudah berubah, silakan ulangi.");

                $peminjaman->items()->create([
                    'alat_id' => $alat->id,
                    'jumlah' => $item['jumlah'],
                ]);
            }

            return $peminjaman;
        });

        return redirect()
            ->route('peminjaman.show', $peminjaman)
            ->with('sukses', "Pengajuan {$peminjaman->kode} terkirim. Tunggu persetujuan petugas.");
    }

    public function show(Request $request, Peminjaman $peminjaman): View
    {
        $this->pastikanBoleh($request, $peminjaman);

        $peminjaman->load('user', 'petugas', 'items.alat');

        return view('peminjaman.show', compact('peminjaman'));
    }

    public function batal(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $this->pastikanBoleh($request, $peminjaman);

        abort_unless($peminjaman->bisaDibatalkan(), 422, 'Pengajuan ini tidak bisa dibatalkan lagi.');

        DB::transaction(function () use ($peminjaman) {
            if ($peminjaman->status === 'disetujui') {
                foreach ($peminjaman->items as $item) {
                    $item->alat->tambahStok($item->jumlah);
                }
            }

            $peminjaman->update(['status' => 'dibatalkan']);
        });

        return redirect()->route('peminjaman.index')->with('sukses', 'Pengajuan dibatalkan.');
    }

    private function pastikanBoleh(Request $request, Peminjaman $peminjaman): void
    {
        abort_unless(
            $request->user()->isAdmin() || $peminjaman->user_id === $request->user()->id,
            403
        );
    }
}
