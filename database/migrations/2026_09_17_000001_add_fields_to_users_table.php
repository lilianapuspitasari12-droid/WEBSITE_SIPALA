<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('role')->default('siswa')->after('email'); // siswa | guru | admin
            $table->string('kelas_jabatan')->nullable()->after('role'); // "XII RPL 2" atau "Kepala Lab"
            $table->string('no_hp', 20)->nullable()->after('kelas_jabatan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'kelas_jabatan', 'no_hp']);
        });
    }
};
