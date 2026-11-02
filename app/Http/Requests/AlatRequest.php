<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $alat = $this->route('alat');

        return [
            'kode' => ['required', 'string', 'max:50', Rule::unique('alats', 'kode')->ignore($alat)],
            'nama' => ['required', 'string', 'max:150'],
            'kategori_id' => ['nullable', 'exists:kategoris,id'],
            'merk' => ['nullable', 'string', 'max:100'],
            'lokasi' => ['nullable', 'string', 'max:100'],
            'kondisi' => ['required', Rule::in(array_keys(\App\Models\Alat::KONDISI))],
            'stok_total' => ['required', 'integer', 'min:1', 'max:9999'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'aktif' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kode' => 'kode alat',
            'nama' => 'nama alat',
            'stok_total' => 'jumlah stok',
        ];
    }
}
