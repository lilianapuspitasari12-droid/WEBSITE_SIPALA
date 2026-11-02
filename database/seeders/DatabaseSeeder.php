<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Liliana',
            'username' => 'liliana',
            'email' => 'admin@sipala.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'kelas_jabatan' => 'Kepala Laboratorium',
        ]);

        User::create([
            'name' => 'Rian Perkasa',
            'username' => 'rianperkasa',
            'email' => 'rian@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'no_hp' => '081234567890',
            'kelas_jabatan' => 'XII RPL 2',
        ]);

        User::create([
            'name' => 'Wulan Saraswati',
            'username' => 'wulan',
            'email' => 'wulan@sipala.test',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'kelas_jabatan' => 'Guru Produktif TKJ',
        ]);

        $kategoriData = [
    'Komputer dan Laptop' => 'Komputer, laptop, monitor, keyboard, dan mouse.',
    'Jaringan dan Kabel' => 'Router, switch, kabel LAN, dan konektor RJ45.',
    'Multimedia' => 'Kamera, mikrofon, speaker, tripod, dan headset.',
    'Peralatan Praktik' => 'Tang crimping, obeng, dan LAN tester.',
    'Perlengkapan Laboratorium' => 'Proyektor, printer, UPS, dan terminal listrik.',
];
        $kategoris = collect($kategoriData)->mapWithKeys(fn ($ket, $nama) => [
            $nama => Kategori::create(['nama' => $nama, 'keterangan' => $ket]),
        ]);

        $alats = [
    ['ALT-001', 'Laptop Lenovo', 'Komputer dan Laptop', 'Lenovo', 'Lemari A1', 5],
    ['ALT-002', 'Monitor LED', 'Komputer dan Laptop', 'LG', 'Lemari A1', 6],
    ['ALT-003', 'Router WiFi', 'Jaringan dan Kabel', 'TP-Link', 'Lemari B2', 4],
    ['ALT-004', 'Switch 8 Port', 'Jaringan dan Kabel', 'TP-Link', 'Lemari B2', 5],
    ['ALT-005', 'Kamera Digital', 'Multimedia', 'Canon', 'Ruang Media', 3],
    ['ALT-006', 'Headset', 'Multimedia', 'Logitech', 'Ruang Media', 10],
    ['ALT-007', 'Tang Crimping', 'Peralatan Praktik', 'Krisbow', 'Lemari A2', 4],
    ['ALT-008', 'LAN Tester', 'Peralatan Praktik', 'Generic', 'Lemari A2', 5],
    ['ALT-009', 'Proyektor LCD', 'Perlengkapan Laboratorium', 'Epson', 'Ruang Lab', 2],
    ['ALT-010', 'UPS', 'Perlengkapan Laboratorium', 'APC', 'Ruang Lab', 3],
];

        foreach ($alats as [$kode, $nama, $kategori, $merk, $lokasi, $stok]) {
            Alat::create([
                'kategori_id' => $kategoris[$kategori]->id,
                'kode' => $kode,
                'nama' => $nama,
                'merk' => $merk,
                'lokasi' => $lokasi,
                'kondisi' => 'baik',
                'stok_total' => $stok,
                'stok_tersedia' => $stok,
                'deskripsi' => "{$nama} siap dipinjam untuk kegiatan praktik.",
            ]);
        }
    }
}
