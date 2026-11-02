<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Kategori extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'slug', 'keterangan'];

    protected static function booted(): void
    {
        static::saving(function (Kategori $kategori) {
            $kategori->slug = Str::slug($kategori->nama);
        });
    }

    public function alats(): HasMany
    {
        return $this->hasMany(Alat::class);
    }
}
