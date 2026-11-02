<?php

use App\Http\Controllers\Admin\PersetujuanController;
use App\Http\Controllers\Admin\LaporanPeminjamanController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(
    auth()->check() ? 'dashboard' : 'login'
))->name('beranda');

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    // Profil pengguna
    Route::get('/profil', [ProfileController::class, 'edit'])
        ->name('profil.edit');

    Route::put('/profil', [ProfileController::class, 'update'])
        ->name('profil.update');

    Route::delete('/profil/foto', [ProfileController::class, 'hapusFoto'])
        ->name('profil.foto.hapus');

    // Notifikasi
    Route::get('/notifikasi/{notification}', [NotificationController::class, 'show'])
        ->name('notifikasi.show');

    // Katalog alat
    Route::get('/alat', [AlatController::class, 'index'])
        ->name('alat.index');

    Route::get('/alat/{alat}', [AlatController::class, 'show'])
        ->name('alat.show');

    // Peminjaman
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])
        ->name('peminjaman.index');

    Route::get('/peminjaman/ajukan', [PeminjamanController::class, 'create'])
        ->name('peminjaman.create');

    Route::post('/peminjaman', [PeminjamanController::class, 'store'])
        ->name('peminjaman.store');

    Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show'])
        ->name('peminjaman.show');

    Route::patch('/peminjaman/{peminjaman}/batal', [PeminjamanController::class, 'batal'])
        ->name('peminjaman.batal');

    // Pengembalian dan bukti
    Route::get('/pengembalian', [PengembalianController::class, 'index'])
        ->name('pengembalian.index');

    Route::get('/pengembalian/{peminjaman}', [PengembalianController::class, 'show'])
        ->name('pengembalian.show');

    Route::post('/peminjaman/{peminjaman}/pengembalian', [PengembalianController::class, 'store'])
        ->name('pengembalian.store');

    // Riwayat peminjaman
    Route::get('/riwayat-transaksi', [PengembalianController::class, 'riwayat'])
        ->name('riwayat.index');

    // Khusus admin/petugas
    Route::middleware('admin')->group(function () {

        // Kelola alat
        Route::get('/kelola-alat/tambah', [AlatController::class, 'create'])
            ->name('alat.create');

        Route::post('/kelola-alat', [AlatController::class, 'store'])
            ->name('alat.store');

        Route::get('/kelola-alat/{alat}/ubah', [AlatController::class, 'edit'])
            ->name('alat.edit');

        Route::put('/kelola-alat/{alat}', [AlatController::class, 'update'])
            ->name('alat.update');

        Route::delete('/kelola-alat/{alat}', [AlatController::class, 'destroy'])
            ->name('alat.destroy');

        // Persetujuan peminjaman
        Route::get('/persetujuan', [PersetujuanController::class, 'index'])
            ->name('persetujuan.index');

        Route::patch('/persetujuan/{peminjaman}/setujui', [PersetujuanController::class, 'setujui'])
            ->name('persetujuan.setujui');

        Route::patch('/persetujuan/{peminjaman}/tolak', [PersetujuanController::class, 'tolak'])
            ->name('persetujuan.tolak');

        Route::patch('/persetujuan/{peminjaman}/serahkan', [PersetujuanController::class, 'serahkan'])
            ->name('persetujuan.serahkan');

        Route::patch('/persetujuan/{peminjaman}/kembalikan', [PersetujuanController::class, 'kembalikan'])
            ->name('persetujuan.kembalikan');

        // Verifikasi pengembalian
        Route::patch(
            '/pengembalian/{pengembalian}/konfirmasi',
            [PengembalianController::class, 'konfirmasi']
        )->name('pengembalian.konfirmasi');

        Route::patch(
            '/pengembalian/{pengembalian}/tolak',
            [PengembalianController::class, 'tolak']
        )->name('pengembalian.tolak');

        // Laporan peminjaman
        Route::get('/laporan-peminjaman', [
            LaporanPeminjamanController::class,
            'index',
        ])->name('laporan.peminjaman');

        Route::get('/laporan-peminjaman/cetak', [
            LaporanPeminjamanController::class,
            'cetak',
        ])->name('laporan.peminjaman.cetak');

        Route::get('/laporan-peminjaman/word', [
            LaporanPeminjamanController::class,
            'word',
        ])->name('laporan.peminjaman.word');
    });
});

require __DIR__.'/auth.php';
