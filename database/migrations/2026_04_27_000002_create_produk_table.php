<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('name_produk');
            $table->text('deskripsi');
            $table->unsignedBigInteger('harga');
            $table->unsignedInteger('stok')->default(0);
            $table->foreignId('kategori_id')
                ->constrained('kategori_produk', 'id_kategori')
                ->restrictOnDelete();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
