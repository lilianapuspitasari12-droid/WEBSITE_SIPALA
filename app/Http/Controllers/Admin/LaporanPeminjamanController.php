<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class LaporanPeminjamanController extends Controller
{
    private function ambilData(Request $request): array
    {
        $request->validate([
            'bulan' => ['nullable', 'regex:/^(all|[1-9]|1[0-2])$/'],
            'tahun' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = $request->input('bulan', 'all');

        $query = Peminjaman::with([
            'user',
            'items.alat',
        ])->whereYear('tanggal_pinjam', $tahun);

        if ($bulan !== 'all') {
            $query->whereMonth('tanggal_pinjam', (int) $bulan);
        }

        $peminjaman = $query
            ->orderByDesc('tanggal_pinjam')
            ->orderByDesc('id')
            ->get();

        $jumlahStatus = [];

        foreach (Peminjaman::STATUS as $status => $label) {
            $jumlahStatus[$status] = $peminjaman
                ->where('status', $status)
                ->count();
        }

        return compact(
            'peminjaman',
            'tahun',
            'bulan',
            'jumlahStatus'
        );
    }

    public function index(Request $request)
    {
        $data = $this->ambilData($request);

        return view('admin.laporan-peminjaman.index', $data);
    }

    public function cetak(Request $request)
    {
        $data = $this->ambilData($request);

        $data['otomatisCetak'] = true;

        return view('admin.laporan-peminjaman.index', $data);
    }

    public function word(Request $request)
    {
        $data = $this->ambilData($request);

        $html = view(
            'admin.laporan-peminjaman.word',
            $data
        )->render();

        $namaFile = 'rekap-peminjaman-'
            . $data['tahun']
            . ($data['bulan'] !== 'all'
                ? '-' . str_pad($data['bulan'], 2, '0', STR_PAD_LEFT)
                : '')
            . '.doc';

        return response()->streamDownload(
            function () use ($html) {
                echo "\xEF\xBB\xBF";
                echo $html;
            },
            $namaFile,
            [
                'Content-Type' => 'application/msword; charset=UTF-8',
            ]
        );
    }
}
