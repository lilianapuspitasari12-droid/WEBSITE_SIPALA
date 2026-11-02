<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlatRequest;
use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AlatController extends Controller
{
    public function index(Request $request): View
    {
        $alats = Alat::with('kategori')
            ->cari($request->query('q'))
            ->when($request->query('kategori'), fn ($q, $id) => $q->where('kategori_id', $id))
            ->when($request->boolean('tersedia'), fn ($q) => $q->tersedia())
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        return view('alat.index', [
            'alats' => $alats,
            'kategoris' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function show(Alat $alat): View
    {
        $alat->load('kategori');

        return view('alat.show', compact('alat'));
    }

    public function create(): View
    {
        return view('alat.form', [
            'alat' => new Alat(['kondisi' => 'baik', 'stok_total' => 1, 'aktif' => true]),
            'kategoris' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(AlatRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['aktif'] = $request->boolean('aktif');
        $data['stok_tersedia'] = $data['stok_total'];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('alat', 'public');
        }

        Alat::create($data);

        return redirect()->route('alat.index')->with('sukses', 'Alat ditambahkan.');
    }

    public function edit(Alat $alat): View
    {
        return view('alat.form', [
            'alat' => $alat,
            'kategoris' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(AlatRequest $request, Alat $alat): RedirectResponse
    {
        $data = $request->validated();
        $data['aktif'] = $request->boolean('aktif');

        // Stok tersedia mengikuti perubahan stok total, tanpa mengganggu unit yang sedang dipinjam.
        $sedangDipinjam = $alat->stok_total - $alat->stok_tersedia;
        $data['stok_tersedia'] = max(0, $data['stok_total'] - $sedangDipinjam);

        if ($request->hasFile('foto')) {
            if ($alat->foto) {
                Storage::disk('public')->delete($alat->foto);
            }
            $data['foto'] = $request->file('foto')->store('alat', 'public');
        }

        $alat->update($data);

        return redirect()->route('alat.index')->with('sukses', 'Perubahan tersimpan.');
    }

    public function destroy(Alat $alat): RedirectResponse
    {
        if ($alat->items()->whereHas('peminjaman', fn ($q) => $q->whereIn('status', ['disetujui', 'dipinjam']))->exists()) {
            return back()->with('gagal', 'Alat ini sedang dipinjam, jadi belum bisa dihapus.');
        }

        if ($alat->foto) {
            Storage::disk('public')->delete($alat->foto);
        }

        $alat->delete();

        return redirect()->route('alat.index')->with('sukses', 'Alat dihapus.');
    }
}
