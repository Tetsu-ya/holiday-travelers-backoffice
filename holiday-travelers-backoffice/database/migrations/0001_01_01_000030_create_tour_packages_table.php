<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['domestic', 'international']);
            $table->string('destination');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('duration_days');
            $table->integer('slots')->default(0);
            $table->foreignId('primary_supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tour_packages'); }
};
