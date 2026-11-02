<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PersetujuanController extends Controller
{
    public function index(Request $request): View
    {
        $peminjamans = Peminjaman::with('user', 'items.alat')
            ->status($request->query('status', 'menunggu'))
            ->oldest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.persetujuan', [
            'peminjamans' => $peminjamans,
            'statusAktif' => $request->query('status', 'menunggu'),
        ]);
    }

    public function setujui(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        abort_unless($peminjaman->status === 'menunggu', 422, 'Pengajuan ini sudah diproses.');

        DB::transaction(function () use ($request, $peminjaman) {
            foreach ($peminjaman->items as $item) {
                $alat = $item->alat()->lockForUpdate()->first();

                abort_if(
                    $item->jumlah > $alat->stok_tersedia,
                    422,
                    "Stok {$alat->nama} tinggal {$alat->stok_tersedia} unit."
                );

                $alat->kurangiStok($item->jumlah);
            }

            $peminjaman->update([
                'status' => 'disetujui',
                'catatan_petugas' => $request->input('catatan_petugas'),
                'diproses_oleh' => $request->user()->id,
                'diproses_pada' => now(),
            ]);
        });

        return back()->with('sukses', "{$peminjaman->kode} disetujui.");
    }

    public function tolak(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $request->validate(['catatan_petugas' => ['required', 'string', 'max:500']]);

        abort_unless($peminjaman->status === 'menunggu', 422, 'Pengajuan ini sudah diproses.');

        $peminjaman->update([
            'status' => 'ditolak',
            'catatan_petugas' => $request->input('catatan_petugas'),
            'diproses_oleh' => $request->user()->id,
            'diproses_pada' => now(),
        ]);

        return back()->with('sukses', "{$peminjaman->kode} ditolak.");
    }

    public function serahkan(Peminjaman $peminjaman): RedirectResponse
    {
        abort_unless($peminjaman->status === 'disetujui', 422, 'Alat hanya bisa diserahkan setelah disetujui.');

        $peminjaman->update(['status' => 'dipinjam']);

        return back()->with('sukses', "Alat untuk {$peminjaman->kode} diserahkan.");
    }

    public function kembalikan(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        abort_unless($peminjaman->status === 'dipinjam', 422, 'Peminjaman ini tidak sedang berjalan.');

        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.jumlah_kembali' => ['required', 'integer', 'min:0'],
            'items.*.kondisi_kembali' => ['required', 'in:baik,rusak,hilang'],
            'items.*.catatan' => ['nullable', 'string', 'max:255'],
            'catatan_petugas' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($data, $peminjaman) {
            foreach ($peminjaman->items as $item) {
                $masuk = $data['items'][$item->id] ?? null;

                if (! $masuk) {
                    continue;
                }

                $jumlahKembali = min((int) $masuk['jumlah_kembali'], $item->jumlah);

                $item->update([
                    'jumlah_kembali' => $jumlahKembali,
                    'kondisi_kembali' => $masuk['kondisi_kembali'],
                    'catatan' => $masuk['catatan'] ?? null,
                ]);

                // Hanya unit yang kembali dalam kondisi baik yang masuk lagi ke stok.
                if ($masuk['kondisi_kembali'] === 'baik') {
                    $item->alat->tambahStok($jumlahKembali);
                } elseif ($masuk['kondisi_kembali'] === 'rusak') {
                    $item->alat->tambahStok($jumlahKembali);
                    $item->alat->update(['kondisi' => 'rusak_ringan']);
                } else {
                    $item->alat->decrement('stok_total', $jumlahKembali);
                }
            }

            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => now(),
                'catatan_petugas' => $data['catatan_petugas'] ?? $peminjaman->catatan_petugas,
            ]);
        });

        return back()->with('sukses', "{$peminjaman->kode} selesai. Alat sudah dicatat kembali.");
    }
}
