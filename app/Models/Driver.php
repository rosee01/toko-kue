<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'no_telepon', 'jenis_kendaraan', 'plat_nomor', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'kurir_id');
    }
}