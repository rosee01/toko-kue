<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('zona_pengiriman', 20)->nullable()->after('ongkir');
            $table->timestamp('jadwal_diminta')->nullable()->after('zona_pengiriman');
            $table->timestamp('jadwal_pengiriman')->nullable()->after('jadwal_diminta');
            $table->boolean('jadwal_dikonfirmasi')->default(false)->after('jadwal_pengiriman');
            $table->boolean('izin_notifikasi_whatsapp')->default(false)->after('jadwal_dikonfirmasi');
        });

        Schema::create('notifikasi_whatsapp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->nullable()->constrained('pesanan')->nullOnDelete();
            $table->string('kode_pesanan', 40)->nullable()->index();
            $table->string('telepon', 20);
            $table->string('peristiwa', 60);
            $table->string('status', 20)->default('pending')->index();
            $table->string('provider_message_id')->nullable();
            $table->text('pesan_error')->nullable();
            $table->timestamp('dikirim_pada')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_whatsapp');
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn([
                'zona_pengiriman',
                'jadwal_diminta',
                'jadwal_pengiriman',
                'jadwal_dikonfirmasi',
                'izin_notifikasi_whatsapp',
            ]);
        });
    }
};
