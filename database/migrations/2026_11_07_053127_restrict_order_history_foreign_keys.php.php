<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat pesanan tidak boleh ikut terhapus saat menu/paket/customer dihapus.
     * Sebelumnya cascadeOnDelete => hapus kategori/menu/paket/customer menghapus riwayat transaksi.
     */
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['menu_id']);
            $table->foreign('menu_id')->references('id')->on('menus')->restrictOnDelete();
        });

        Schema::table('order_packages', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->foreign('package_id')->references('id')->on('packages')->restrictOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('customers')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['menu_id']);
            $table->foreign('menu_id')->references('id')->on('menus')->cascadeOnDelete();
        });

        Schema::table('order_packages', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->foreign('package_id')->references('id')->on('packages')->cascadeOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });
    }
};