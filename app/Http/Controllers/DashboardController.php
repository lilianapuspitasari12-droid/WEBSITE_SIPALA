<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return view('dashboard-admin', [
                'totalAlat' => Alat::where('aktif', true)->count(),
                'unitTersedia' => Alat::where('aktif', true)->sum('stok_tersedia'),
                'menunggu' => Peminjaman::where('status', 'menunggu')->count(),
                'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
                'terlambat' => Peminjaman::terlambat()->count(),
                'antrean' => Peminjaman::with('user', 'items.alat')
                    ->where('status', 'menunggu')
                    ->oldest()
                    ->take(5)
                    ->get(),
                'stokMenipis' => Alat::where('aktif', true)
                    ->where('stok_tersedia', '<=', 3)
                    ->orderBy('stok_tersedia')
                    ->take(5)
                    ->get(),
            ]);
        }

        return view('dashboard', [
            'aktif' => Peminjaman::with('items.alat')
                ->milik($user)
                ->whereIn('status', ['menunggu', 'disetujui', 'dipinjam'])
                ->latest()
                ->get(),
            'riwayat' => Peminjaman::milik($user)
                ->whereIn('status', ['dikembalikan', 'ditolak', 'dibatalkan'])
                ->latest()
                ->take(5)
                ->get(),
            'alatPopuler' => Alat::tersedia()->withCount('items')->orderByDesc('items_count')->take(4)->get(),
        ]);
    }
}
