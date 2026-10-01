<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan');
            // Produk boleh terhapus; nama & harga disimpan sebagai snapshot agar riwayat tetap utuh.
            $table->foreignId('produk_id')
                ->nullable()
                ->constrained('produk', 'id_produk')
                ->nullOnDelete();
            $table->string('menu');
            $table->unsignedBigInteger('harga_satuan');
            $table->unsignedInteger('jumlah')->default(1);
            $table->unsignedBigInteger('total');
            $table->string('status')->default('Pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
