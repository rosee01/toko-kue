<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesanan extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'Pending';
    public const STATUS_DIPROSES = 'Diproses';
    public const STATUS_SIAP_DIANTAR = 'Siap Diantar';
    public const STATUS_DALAM_PENGANTARAN = 'Dalam Pengantaran';
    public const STATUS_SELESAI = 'Selesai';
    public const STATUS_DIBATALKAN = 'Dibatalkan';
    public const PEMBAYARAN_BELUM_DIBAYAR = 'Belum Dibayar';
    public const PEMBAYARAN_MENUNGGU_VERIFIKASI = 'Menunggu Verifikasi';
    public const PEMBAYARAN_LUNAS = 'Lunas';
    public const PEMBAYARAN_COD = 'COD';
    public const PEMBAYARAN_DITOLAK = 'Ditolak';
    public const PEMBAYARAN_DIBATALKAN = 'Dibatalkan';
    public const METODE_TRANSFER_BANK = 'transfer_bank';
    public const METODE_E_WALLET = 'e_wallet';
    public const METODE_COD = 'cod';
    public const METODE_PEMBAYARAN = [
        self::METODE_TRANSFER_BANK,
        self::METODE_E_WALLET,
        self::METODE_COD,
    ];

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_DIPROSES,
        self::STATUS_SIAP_DIANTAR,
        self::STATUS_DALAM_PENGANTARAN,
        self::STATUS_SELESAI,
    ];

    protected $table = 'pesanan';

    protected $fillable = [
        'user_id',
        'kode_pesanan',
        'nama_pelanggan',
        'alamat_pengiriman',
        'no_telepon',
        'catatan',
        'produk_id',
        'menu',
        'harga_satuan',

        'jumlah',
        'total',
        'status',
        'status_pembayaran',
        'metode_pembayaran',
        'bukti_pembayaran',
        'pembayaran_dikirim_pada',
        'tanggal_pembayaran',
        'catatan_pembayaran',
        'ongkir',
        'zona_pengiriman',
        'jenis_pengiriman',
        'jarak_pengiriman_km',
        'ongkir_dikonfirmasi',
        'jadwal_diminta',
        'jadwal_pengiriman',
        'jadwal_dikonfirmasi',
        'izin_notifikasi_whatsapp',
        'kurir_id',
    ];

    protected function casts(): array
    {
        return [
            'harga_satuan' => 'integer',
            'jumlah' => 'integer',
            'total' => 'integer',
            'ongkir' => 'integer',
            'jarak_pengiriman_km' => 'decimal:2',
            'ongkir_dikonfirmasi' => 'boolean',
            'jadwal_diminta' => 'datetime',
            'jadwal_pengiriman' => 'datetime',
            'jadwal_dikonfirmasi' => 'boolean',
            'izin_notifikasi_whatsapp' => 'boolean',
            'tanggal_pembayaran' => 'datetime',
            'pembayaran_dikirim_pada' => 'datetime',
        ];
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'id_produk');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'kurir_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLabelMetodePembayaranAttribute(): string
    {
        return match ($this->metode_pembayaran) {
            self::METODE_TRANSFER_BANK => 'Transfer bank',
            self::METODE_E_WALLET => 'E-wallet',
            self::METODE_COD => 'Bayar di tempat (COD)',
            default => $this->status_pembayaran === self::PEMBAYARAN_COD ? 'Bayar di tempat (COD)' : 'Belum dicatat',
        };
    }

    /**
     * Link WhatsApp dari nomor telepon pelanggan (08xx / 8xx / +62xx jadi 62xx).
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        $nomor = preg_replace('/\D+/', '', (string) $this->no_telepon);


        if ($nomor === '') {
            return null;
        }

        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        } elseif (str_starts_with($nomor, '8')) {
            $nomor = '62' . $nomor;
        }

        return 'https://wa.me/' . $nomor;
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    public function scopeAntara($query, ?string $dari, ?string $sampai)
    {
        return $query
            ->when($dari, fn ($q) => $q->whereDate('created_at', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('created_at', '<=', $sampai));
    }
}