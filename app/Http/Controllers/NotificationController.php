<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function show(string $notification): RedirectResponse
    {
        // Cari notifikasi milik pengguna yang sedang login.
        $dataNotifikasi = auth()->user()
            ->notifications()
            ->where('id', $notification)
            ->firstOrFail();

        // Tandai notifikasi sebagai sudah dibaca.
        $dataNotifikasi->markAsRead();

        // Ambil ID peminjaman dari data notifikasi.
        $peminjamanId = $dataNotifikasi->data['peminjaman_id'] ?? null;

        // Pastikan peminjaman memang milik pengguna tersebut.
        $peminjaman = Peminjaman::where('user_id', auth()->id())
            ->findOrFail($peminjamanId);

        return redirect()->route('peminjaman.show', $peminjaman);
    }
}
