<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 120);
            $table->string('no_telepon', 20);
            $table->string('jenis_kendaraan', 60)->nullable();
            $table->string('plat_nomor', 16)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->foreign('kurir_id')->references('id')->on('drivers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropForeign(['kurir_id']);
        });

        Schema::dropIfExists('drivers');
    }
};