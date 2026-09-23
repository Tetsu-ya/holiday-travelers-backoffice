<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('discount_code_id')->nullable()->after('total_amount')->constrained('discount_codes')->nullOnDelete();
            $table->decimal('subtotal_amount', 12, 2)->nullable()->after('discount_code_id');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('subtotal_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['discount_code_id']);
            $table->dropColumn(['discount_code_id', 'subtotal_amount', 'discount_amount']);
        });
    }
};
