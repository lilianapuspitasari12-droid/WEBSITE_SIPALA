{{-- resources/views/komponen-status.blade.php --}}
@php
    $warna = [
        'menunggu' => 'text-kuning',
        'disetujui' => 'text-biru',
        'dipinjam' => 'text-tosca',
        'dikembalikan' => 'text-kabut',
        'ditolak' => 'text-bata',
        'dibatalkan' => 'text-kabut',
    ][$status] ?? 'text-kabut';
@endphp
<span class="inline-flex items-center gap-2 text-sm font-medium {{ $warna }}">
    <span class="h-2 w-2 rounded-full bg-current"></span>
    {{ \App\Models\Peminjaman::STATUS[$status] ?? $status }}
</span>
