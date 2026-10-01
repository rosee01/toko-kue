<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifikasiWhatsApp extends Model
{
    protected $table = 'notifikasi_whatsapp';

    protected $fillable = [
        'pesanan_id',
        'kode_pesanan',
        'telepon',
        'peristiwa',
        'status_pesan',
        'detail_pesan',
        'status',
        'pesan_error',
        'provider_message_id',
        'dikirim_pada',
    ];

    protected function casts(): array
    {
        return ['dikirim_pada' => 'datetime'];
    }
}
