<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->text('description')->nullable();
            $table->string('discount_type')->default('percentage'); $table->decimal('discount_value', 12, 2);
            $table->date('starts_on'); $table->date('ends_on'); $table->string('status')->default('draft'); $table->timestamps();
        });
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id(); $table->string('code')->unique(); $table->string('description')->nullable();
            $table->string('discount_type')->default('percentage'); $table->decimal('discount_value', 12, 2);
            $table->unsignedInteger('usage_limit')->nullable(); $table->unsignedInteger('used_count')->default(0);
            $table->date('starts_on'); $table->date('ends_on'); $table->string('status')->default('active'); $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_codes'); Schema::dropIfExists('promotions');
    }
};