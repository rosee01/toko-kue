<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdukRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name_produk' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:500'],
            'harga' => ['required', 'integer', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'stok_minimum' => ['required', 'integer', 'min:0'],
            'is_active' => ['required', 'boolean'],
            'kategori_id' => ['required', 'exists:kategori_produk,id_kategori'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'fotos' => ['sometimes', 'array', 'max:5'],
            'fotos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_produk' => 'nama produk',
            'kategori_id' => 'kategori',
            'stok_minimum' => 'stok minimum',
            'fotos.*' => 'foto produk',
        ];
    }
}
