<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi_whatsapp', function (Blueprint $table) {
            $table->json('data_pesan')->nullable()->after('peristiwa');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi_whatsapp', function (Blueprint $table) {
            $table->dropColumn('data_pesan');
        });
    }
};
