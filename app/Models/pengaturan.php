<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';
    protected $fillable = ['kunci', 'nilai'];

    public static function ambil(string $kunci, mixed $default = null): mixed
    {
        try {
            $nilai = static::where('kunci', $kunci)->value('nilai');

            return $nilai !== null && $nilai !== '' ? $nilai : $default;
        } catch (\Throwable) {
            return $default; // tabel belum dimigrasi
        }
    }

    public static function simpan(string $kunci, ?string $nilai): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
    }
}