<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Validasi unique di aplikasi bisa kalah oleh dua pendaftaran bersamaan; kunci juga di database.
    // Jika gagal karena sudah ada email ganda, rapikan datanya dulu lalu jalankan ulang.
    public function up(): void
    {
        Schema::table('customer_accounts', function (Blueprint $table) {
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('customer_accounts', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }
};