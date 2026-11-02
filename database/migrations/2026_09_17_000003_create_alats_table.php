<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->nullable()->constrained('kategoris')->nullOnDelete();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('merk')->nullable();
            $table->string('lokasi')->nullable();       // lemari / ruang penyimpanan
            $table->string('kondisi')->default('baik'); // baik | rusak_ringan | rusak_berat
            $table->unsignedInteger('stok_total')->default(1);
            $table->unsignedInteger('stok_tersedia')->default(1);
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alats');
    }
};
