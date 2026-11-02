<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
{
    $user = auth()->user()->fresh();

    return view('profil.edit', compact('user'));
}

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validasi data profil
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],

            'nomor_hp' => ['nullable', 'string', 'max:20'],

            'kelas_jabatan' => ['nullable', 'string', 'max:100'],

            'foto_profil' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'password_lama' => [
                'nullable',
                'required_with:password_baru',
                'current_password',
            ],

            'password_baru' => [
                'nullable',
                'required_with:password_lama',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'foto_profil.image' => 'File harus berupa gambar.',
            'foto_profil.mimes' => 'Foto harus JPG, JPEG, PNG, atau WEBP.',
            'foto_profil.max' => 'Ukuran foto maksimal 2 MB.',
            'password_lama.required_with' => 'Password lama wajib diisi.',
            'password_lama.current_password' => 'Password lama tidak sesuai.',
            'password_baru.required_with' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Simpan foto baru
        if ($request->hasFile('foto_profil')) {
            $fotoBaru = $request->file('foto_profil')
                ->store('foto-profil', 'public');

            if (!$fotoBaru) {
                return back()->withInput()
                    ->withErrors(['foto_profil' => 'Foto gagal diunggah.']);
            }

            // Hapus foto lama setelah foto baru berhasil diunggah
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            $user->foto_profil = $fotoBaru;
        }

        // Perbarui informasi pengguna
        $user->name = $data['name'];
        $user->username = $data['username'];
        $user->nomor_hp = $data['nomor_hp'] ?? null;
        $user->kelas_jabatan = $data['kelas_jabatan'] ?? null;

        // Password hanya berubah jika diisi
        if (!empty($data['password_baru'])) {
            $user->password = $data['password_baru'];
        }

        $user->save();

        return redirect()
            ->route('profil.edit')
            ->with('sukses', 'Profil berhasil diperbarui.');
    }

    public function hapusFoto(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);

            $user->foto_profil = null;
            $user->save();
        }

        return redirect()
            ->route('profil.edit')
            ->with('sukses', 'Foto profil berhasil dihapus.');
    }
}
