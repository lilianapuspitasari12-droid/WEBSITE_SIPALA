<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DendaController extends Controller
{
    /**
     * Menampilkan daftar denda peminjaman.
     */
    public function index()
    {
        $peminjamans = Peminjaman::with([
            'user',
            'items.alat',
            'adminDenda',
        ])
            ->where(function ($query) {
                $query
                    // Peminjaman yang masih terlambat
                    ->where(function ($q) {
                        $q->where('status', 'dipinjam')
                            ->whereDate(
                                'tanggal_jatuh_tempo',
                                '<',
                                today()
                            );
                    })

                    // Peminjaman yang sudah memiliki catatan denda
                    ->orWhere('total_denda', '>', 0);
            })
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->paginate(10);

        return view('admin.denda.index', compact('peminjamans'));
    }

    /**
     * Menandai denda sebagai lunas.
     */
    public function lunas(Request $request, Peminjaman $peminjaman)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $hasil = DB::transaction(function () use ($peminjaman, $request) {
            $data = Peminjaman::whereKey($peminjaman->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Denda hanya bisa dilunasi setelah alat dikembalikan.
            if ($data->status !== 'dikembalikan') {
                return 'belum_dikembalikan';
            }

            if ((int) $data->total_denda <= 0) {
                return 'tidak_ada_denda';
            }

            if ($data->status_denda === 'lunas') {
                return 'sudah_lunas';
            }

            $data->status_denda = 'lunas';
            $data->denda_dibayar_pada = now();
            $data->denda_dibayar_oleh = $request->user()->id;

            $data->save();

            return 'berhasil';
        });

        if ($hasil === 'belum_dikembalikan') {
            return back()->with(
                'error',
                'Denda baru bisa dilunasi setelah alat dikembalikan.'
            );
        }

        if ($hasil === 'tidak_ada_denda') {
            return back()->with(
                'error',
                'Peminjaman ini tidak memiliki denda.'
            );
        }

        if ($hasil === 'sudah_lunas') {
            return back()->with(
                'error',
                'Denda ini sudah tercatat lunas.'
            );
        }

        return back()->with(
            'success',
            'Pembayaran denda berhasil dicatat sebagai lunas.'
        );
    }
}