<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'numeric',
                'digits_between:1,20',
                'unique:users,username',
            ],
            'nomor_hp' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]+$/',
                'unique:users,nomor_hp',
            ],
            'kelas_jabatan' => ['required', 'string', 'max:50'],
            'role' => ['required', 'in:siswa,guru'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [], [
            'name' => 'nama lengkap',
            'kelas_jabatan' => 'kelas / jabatan',
            'role' => 'status pengguna',
        ]);

        $user = User::create($data);

        event(new Registered($user));

        return redirect()
            ->route('login')
            ->with('status', 'Akun berhasil dibuat! Silakan masuk menggunakan akun kamu.');
    }
}
