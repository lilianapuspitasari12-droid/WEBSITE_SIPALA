<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalians', function (Blueprint $table) {
            $table->id();

            $table->foreignId('peminjaman_id')
                ->unique()
                ->constrained('peminjamans')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('bukti_path');
            $table->string('bukti_mime')->nullable();
            $table->text('catatan')->nullable();

            // Menyimpan kondisi setiap alat yang diajukan untuk dikembalikan.
            $table->json('detail_items');

            // menunggu, ditolak, dikonfirmasi
            $table->string('status')->default('menunggu');

            $table->text('catatan_petugas')->nullable();

            $table->foreignId('diproses_oleh')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('diproses_pada')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalians');
    }
};
