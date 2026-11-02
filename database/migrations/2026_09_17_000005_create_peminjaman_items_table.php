<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alat_id')->constrained('alats')->restrictOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
            $table->unsignedInteger('jumlah_kembali')->default(0);
            $table->string('kondisi_kembali')->nullable(); // baik | rusak | hilang
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['peminjaman_id', 'alat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_items');
    }
};
