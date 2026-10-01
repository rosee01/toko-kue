<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable()->after('metode_pembayaran');
            $table->timestamp('pembayaran_dikirim_pada')->nullable()->after('bukti_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['bukti_pembayaran', 'pembayaran_dikirim_pada']);
        });
    }
};
