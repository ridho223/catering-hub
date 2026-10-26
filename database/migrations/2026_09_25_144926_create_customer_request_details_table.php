<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_request_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_request_id')
                ->constrained('customer_requests')
                ->cascadeOnDelete();

            $table->foreignId('menu_id')
                ->constrained('menus')
                ->cascadeOnDelete();

            $table->integer('quantity');

            $table->decimal('requested_price', 15, 2)->nullable();
            $table->decimal('approved_price', 15, 2)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_request_details');
    }
};