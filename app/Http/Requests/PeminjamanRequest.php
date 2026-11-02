<?php

namespace App\Http\Requests;

use App\Models\Alat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class PeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_pinjam' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'keperluan' => ['required', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.alat_id' => ['required', 'distinct', 'exists:alats,id'],
            'items.*.jumlah' => ['required', 'integer', 'min:1'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                foreach ($this->input('items', []) as $i => $item) {
                    $alat = Alat::find($item['alat_id'] ?? null);

                    if (! $alat) {
                        continue;
                    }

                    if (! $alat->aktif) {
                        $validator->errors()->add("items.{$i}.alat_id", "{$alat->nama} sedang tidak dipinjamkan.");

                        continue;
                    }

                    if ((int) ($item['jumlah'] ?? 0) > $alat->stok_tersedia) {
                        $validator->errors()->add(
                            "items.{$i}.jumlah",
                            "Stok {$alat->nama} tinggal {$alat->stok_tersedia}."
                        );
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Pilih minimal satu alat.',
            'items.*.alat_id.distinct' => 'Alat yang sama dipilih dua kali.',
            'tanggal_pinjam.after_or_equal' => 'Tanggal pinjam tidak boleh di masa lalu.',
            'tanggal_jatuh_tempo.after_or_equal' => 'Tanggal kembali harus setelah tanggal pinjam.',
        ];
    }
}
