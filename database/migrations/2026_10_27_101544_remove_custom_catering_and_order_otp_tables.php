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
        Schema::dropIfExists('customer_request_packages');
        Schema::dropIfExists('custom_request_details');
        Schema::dropIfExists('customer_requests');
        Schema::dropIfExists('order_otps');
    }

    public function down(): void
    {
        throw new \RuntimeException(
            'Migration penghapusan tabel custom catering dan OTP tidak dapat dibatalkan otomatis.'
        );
    }
};
