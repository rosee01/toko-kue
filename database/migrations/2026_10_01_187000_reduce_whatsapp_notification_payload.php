<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi_whatsapp', function (Blueprint $table) {
            $table->dropColumn('data_pesan');
            $table->string('status_pesan', 100)->nullable()->after('peristiwa');
            $table->text('detail_pesan')->nullable()->after('status_pesan');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi_whatsapp', function (Blueprint $table) {
            $table->dropColumn(['status_pesan', 'detail_pesan']);
            $table->json('data_pesan')->nullable()->after('peristiwa');
        });
    }
};
