<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->enum('delivery_method', [
                'pickup',
                'delivery'
            ])->default('pickup')->after('delivery_date');

            $table->decimal('delivery_fee', 12, 2)
                ->default(0)
                ->after('delivery_method');

            $table->decimal('delivery_distance', 8, 2)
                ->nullable()
                ->after('delivery_fee');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_method',
                'delivery_fee',
                'delivery_distance',
            ]);
        });
    }
};