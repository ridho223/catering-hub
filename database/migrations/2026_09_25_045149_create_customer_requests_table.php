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
        Schema::create('customer_requests', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('guest_count');
            $table->date('event_date');
            $table->text('address');
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->decimal('final_price',15, 2)->nullable();
            $table->text('admin_notes')->nullable();

            $table->enum('status',[
                'Diajukan',
                'Ditinjau',
                'Menunggu Persetujuan',
                'Disetujui',
                'Ditolak',
                'Selesai'
            ]);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_requests');
    }
};
