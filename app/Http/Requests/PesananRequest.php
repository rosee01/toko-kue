<?php

namespace App\Http\Requests;

use App\Models\Pesanan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PesananRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'nama_pelanggan' => ['required', 'string', 'max:255'],
            'produk_id' => ['required', 'exists:produk,id_produk'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:1000'],
        ];

        if ($this->isMethod('POST')) {
            $rules['status'] = ['required', Rule::in([Pesanan::STATUS_PENDING])];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'nama_pelanggan' => 'nama pelanggan',
            'produk_id' => 'menu',
        ];
    }
}
