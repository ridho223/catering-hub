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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_status',
                'payment_proof',
                'paid_at',
            ]);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique('payments_payment_method_unique');
            $table->dropColumn('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_proof')->nullable();
            $table->timestamp('paid_at')->nullable();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->unique();
        });
    }
};
