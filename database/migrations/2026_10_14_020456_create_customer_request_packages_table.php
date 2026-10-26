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
        Schema::create('customer_request_packages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_request_id')->constrained('customer_requests')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->restrictOnDelete();

            $table->unsignedInteger('quantity');
            $table->decimal('price', 12, 2)->default(0);

            $table->timestamps();

            $table->unique(
                ['customer_request_id', 'package_id'],
                'customer_request_pakages_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_request_packages');
    }
};
