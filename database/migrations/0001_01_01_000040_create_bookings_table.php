<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignId('tour_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('business_partner_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->integer('pax')->default(1);
            $table->date('travel_date');
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'refunded', 'cancelled'])->default('unpaid');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'refunded'])->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('bookings'); }
};
