
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
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

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->foreignId('package_detail_id')
                ->nullable()
                ->constrained('package_details')
                ->nullOnDelete();

            $table->foreignId('replaces_menu_id')
                ->nullable()
                ->constrained('menus')
                ->nullOnDelete();
        });
    }
};
