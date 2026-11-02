
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('
            CREATE TABLE peminjaman_items_baru (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                peminjaman_id INTEGER NOT NULL,
                alat_id INTEGER NOT NULL,
                jumlah INTEGER NOT NULL DEFAULT 1,
                jumlah_kembali INTEGER NOT NULL DEFAULT 0,
                kondisi_kembali VARCHAR,
                catatan TEXT,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY (peminjaman_id) REFERENCES peminjamans(id) ON DELETE CASCADE,
                FOREIGN KEY (alat_id) REFERENCES alats(id) ON DELETE RESTRICT,
                UNIQUE (peminjaman_id, alat_id)
            )
        ');

        DB::statement('
            INSERT INTO peminjaman_items_baru
            SELECT * FROM peminjaman_items
        ');

        DB::statement('DROP TABLE peminjaman_items');

        DB::statement('ALTER TABLE peminjaman_items_baru RENAME TO peminjaman_items');

        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Perbaikan foreign key tidak dibatalkan otomatis.
    }
};