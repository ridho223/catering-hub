<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('custom_request_details', function (Blueprint $table) {
            $table->string('customization_type')->default('additional')->after('menu_id');

            $table->foreignId('replaces_menu_id')->nullable()->after('customization_type')->constrained('menus')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_request_details', function (Blueprint $table) {
            $table->dropForeign(['replaces_menu_id']);
            $table->dropColumn([
                'customization_type',
                'replaces_menu_id',
            ]);
        });
    }
};
