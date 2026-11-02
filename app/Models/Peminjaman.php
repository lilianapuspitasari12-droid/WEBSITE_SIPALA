<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamans';

    protected $fillable = [
        'kode',
        'user_id',
        'tanggal_pinjam',
        'tanggal_jatuh_tempo',
        'tanggal_kembali',
        'keperluan',
        'status',
        'catatan_petugas',
        'diproses_oleh',
        'diproses_pada',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_kembali' => 'date',
        'diproses_pada' => 'datetime',
    ];

    public const STATUS = [
        'menunggu' => 'Menunggu persetujuan',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'dipinjam' => 'Sedang dipinjam',
        'dikembalikan' => 'Sudah dikembalikan',
        'dibatalkan' => 'Dibatalkan',
    ];

    // Relasi pengguna peminjam
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi petugas yang memproses peminjaman
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    // Relasi daftar alat yang dipinjam
    public function items(): HasMany
    {
        return $this->hasMany(PeminjamanItem::class);
    }

    // Relasi data pengembalian
    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class);
    }

    // Mengambil peminjaman milik pengguna tertentu
    public function scopeMilik(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    // Filter berdasarkan status peminjaman
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when(
            $status,
            fn ($q) => $q->where('status', $status)
        );
    }

    // Mengambil peminjaman yang sudah terlambat
    public function scopeTerlambat(Builder $query): Builder
    {
        return $query
            ->where('status', 'dipinjam')
            ->whereDate('tanggal_jatuh_tempo', '<', today());
    }

    // Menampilkan nama status
    public function statusLabel(): string
    {
        return self::STATUS[$this->status] ?? $this->status;
    }

    // Memeriksa apakah peminjaman terlambat
    public function isTerlambat(): bool
    {
        return $this->status === 'dipinjam'
            && $this->tanggal_jatuh_tempo !== null
            && $this->tanggal_jatuh_tempo->lt(today());
    }

    // Menghitung jumlah hari keterlambatan
    public function hariTerlambat(): int
    {
        return $this->isTerlambat()
            ? (int) $this->tanggal_jatuh_tempo->diffInDays(today())
            : 0;
    }

    // Memeriksa apakah peminjaman dapat dibatalkan
    public function bisaDibatalkan(): bool
    {
        return in_array(
            $this->status,
            ['menunggu', 'disetujui'],
            true
        );
    }

    // Membuat kode peminjaman otomatis
    public static function buatKode(): string
    {
        $prefix = 'PJM-' . now()->format('Ymd') . '-';

        $urutan = static::where(
            'kode',
            'like',
            $prefix . '%'
        )->count() + 1;

        return $prefix . str_pad(
            (string) $urutan,
            3,
            '0',
            STR_PAD_LEFT
        );
    }
}
