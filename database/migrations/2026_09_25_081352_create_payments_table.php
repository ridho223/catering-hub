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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();            

            $table->enum('method', [
                'transfer',
                'qris'
            ]);

            $table->decimal('amount', 12, 5);

            $table->enum('status', [
                'Belum Dibayar',
                'Menunggu Verifikasi',
                'Dibayar',
                'Ditolak',
                'Dikembalikan'
            ])->default('Belum Dibayar');

            $table->string('proof')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
