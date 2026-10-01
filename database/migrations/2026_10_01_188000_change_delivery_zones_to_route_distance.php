<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('jenis_pengiriman', 20)->nullable()->after('zona_pengiriman');
            $table->decimal('jarak_pengiriman_km', 8, 2)->nullable()->after('jenis_pengiriman');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['jenis_pengiriman', 'jarak_pengiriman_km']);
        });
    }
};
