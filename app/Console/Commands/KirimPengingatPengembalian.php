<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use App\Notifications\StatusPeminjamanNotification;
use Illuminate\Console\Command;

class KirimPengingatPengembalian extends Command
{
    protected $signature = 'sipala:pengingat-pengembalian';

    protected $description = 'Mengirim pengingat tanggal pengembalian dan keterlambatan alat';

    public function handle(): int
    {
        // Pengingat satu hari sebelum jatuh tempo.
        $besok = Peminjaman::with('user')
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_jatuh_tempo', today()->addDay())
            ->get();

        foreach ($besok as $peminjaman) {
            $this->kirimJikaBelumAda($peminjaman, 'pengingat');
        }

        // Pengingat ketika sudah melewati tanggal jatuh tempo.
        $terlambat = Peminjaman::with('user')
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_jatuh_tempo', '<', today())
            ->get();

        foreach ($terlambat as $peminjaman) {
            $this->kirimJikaBelumAda($peminjaman, 'terlambat');
        }

        $this->info('Pemeriksaan pengingat pengembalian selesai.');

        return self::SUCCESS;
    }

    private function kirimJikaBelumAda(
        Peminjaman $peminjaman,
        string $jenis
    ): void {
        if (! $peminjaman->user) {
            return;
        }

        $sudahDikirim = $peminjaman->user
            ->notifications()
            ->where('type', StatusPeminjamanNotification::class)
            ->where('data->jenis', $jenis)
            ->where('data->peminjaman_id', $peminjaman->id)
            ->exists();

        if ($sudahDikirim) {
            return;
        }

        $peminjaman->user->notify(
            new StatusPeminjamanNotification($peminjaman, $jenis)
        );
    }
}
