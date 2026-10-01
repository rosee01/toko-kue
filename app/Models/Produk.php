<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    use HasFactory;

    public const STOK_MENIPIS = 5;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $fillable = [
        'name_produk',
        'deskripsi',
        'harga',
        'stok',
        'stok_minimum',
        'is_active',
        'kategori_id',
        'foto',
        'foto_galeri',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'stok' => 'integer',
            'stok_minimum' => 'integer',
            'is_active' => 'boolean',
            'foto_galeri' => 'array',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id_kategori');
    }

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'produk_id', 'id_produk');
    }

    public function scopeStokMenipis($query)
    {
        return $query->where('stok', '>', 0)
            ->whereColumn('stok', '<=', 'stok_minimum');
    }
}
