<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeminjamanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'peminjaman_id', 'alat_id', 'jumlah', 'jumlah_kembali', 'kondisi_kembali', 'catatan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'jumlah_kembali' => 'integer',
    ];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class);
    }
}
