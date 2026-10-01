<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function rules(): array
    {
        $unique = Rule::unique('kategori_produk', 'nama_kategori');

        if ($this->route('kategori')) {
            $unique->ignore($this->route('kategori')->id_kategori, 'id_kategori');
        }

        return [
            'nama_kategori' => ['required', 'string', 'max:255', $unique],
            'deskripsi' => ['nullable', 'string', 'max:200'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama_kategori' => 'nama kategori',
            'foto' => 'gambar kategori',
        ];
    }
}
