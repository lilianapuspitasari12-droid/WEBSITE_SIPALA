<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id', 'kode', 'nama', 'merk', 'lokasi', 'kondisi',
        'stok_total', 'stok_tersedia', 'foto', 'deskripsi', 'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'stok_total' => 'integer',
        'stok_tersedia' => 'integer',
    ];

    public const KONDISI = [
        'baik' => 'Baik',
        'rusak_ringan' => 'Rusak ringan',
        'rusak_berat' => 'Rusak berat',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PeminjamanItem::class);
    }

    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('aktif', true)->where('stok_tersedia', '>', 0);
    }

    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        return $query->when($kata, fn ($q) => $q->where(function ($q) use ($kata) {
            $q->where('nama', 'like', "%{$kata}%")
                ->orWhere('kode', 'like', "%{$kata}%")
                ->orWhere('merk', 'like', "%{$kata}%");
        }));
    }

    public function kurangiStok(int $jumlah): void
    {
        $this->decrement('stok_tersedia', $jumlah);
    }

    public function tambahStok(int $jumlah): void
    {
        $this->increment('stok_tersedia', min($jumlah, $this->stok_total - $this->stok_tersedia));
    }
}
