
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->unsignedBigInteger('total_denda')->default(0);
            $table->string('status_denda')->default('tidak_ada');
            $table->dateTime('denda_dibayar_pada')->nullable();
            $table->unsignedBigInteger('denda_dibayar_oleh')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            $table->dropColumn([
                'total_denda',
                'status_denda',
                'denda_dibayar_pada',
                'denda_dibayar_oleh',
            ]);
        });
    }
};
