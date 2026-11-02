<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom nomor HP untuk pendaftaran
            $table->string('nomor_hp', 20)
                ->nullable()
                ->unique();

            // Email tidak wajib karena pendaftaran memakai nomor HP
            $table->string('email')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nomor_hp']);
            $table->dropColumn('nomor_hp');

            $table->string('email')
                ->nullable(false)
                ->change();
        });
    }
};
