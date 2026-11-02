<?php

namespace App\Notifications;

use App\Models\Peminjaman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class StatusPeminjamanNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Peminjaman $peminjaman,
        public string $status,
        public ?string $catatan = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $tanggal = $this->peminjaman->tanggal_jatuh_tempo
            ? $this->peminjaman->tanggal_jatuh_tempo->format('d-m-Y')
            : '-';

        $data = match ($this->status) {
            'disetujui' => [
                'judul' => 'Peminjaman Disetujui',
                'pesan' => 'Pengajuan peminjaman alat kamu telah disetujui oleh Admin.',
                'jenis' => 'status',
            ],

            'ditolak' => [
                'judul' => 'Peminjaman Ditolak',
                'pesan' => 'Pengajuan peminjaman alat kamu ditolak oleh Admin.',
                'jenis' => 'status',
            ],

            'pengingat' => [
                'judul' => 'Pengingat Pengembalian',
                'pesan' => "Peminjaman {$this->peminjaman->kode} harus dikembalikan besok, {$tanggal}.",
                'jenis' => 'pengingat',
            ],

            'terlambat' => [
                'judul' => 'Pengembalian Terlambat',
                'pesan' => "Peminjaman {$this->peminjaman->kode} sudah melewati batas pengembalian {$tanggal}. Segera kembalikan alat.",
                'jenis' => 'terlambat',
            ],

            default => [
                'judul' => 'Informasi Peminjaman',
                'pesan' => 'Ada informasi terbaru mengenai peminjaman alat kamu.',
                'jenis' => 'status',
            ],
        };

        return array_merge($data, [
            'kode' => $this->peminjaman->kode,
            'status' => $this->status,
            'catatan' => $this->catatan,
            'peminjaman_id' => $this->peminjaman->id,
            'url' => route('peminjaman.show', $this->peminjaman),
        ]);
    }
}
