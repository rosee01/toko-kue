<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Tambahkan kolom untuk fitur delivery
            $table->text('alamat_pengiriman')->nullable()->after('nama_pelanggan');
            $table->string('no_telepon', 20)->nullable()->after('alamat_pengiriman');
            $table->text('catatan')->nullable()->after('no_telepon');
            $table->unsignedBigInteger('kurir_id')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['alamat_pengiriman', 'no_telepon', 'catatan', 'kurir_id']);
        });
    }
};