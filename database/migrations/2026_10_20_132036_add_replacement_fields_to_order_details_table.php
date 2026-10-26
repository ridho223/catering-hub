<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->foreignId('package_detail_id')
                ->nullable()
                ->after('source')
                ->constrained('package_details')
                ->nullOnDelete();

            $table->foreignId('replaces_menu_id')
                ->nullable()
                ->after('package_detail_id')
                ->constrained('menus')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropForeign(['package_detail_id']);
            $table->dropForeign(['replaces_menu_id']);

            $table->dropColumn([
                'package_detail_id',
                'replaces_menu_id',
            ]);
        });
    }
};