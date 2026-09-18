<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_contracts', function (Blueprint $table) {
            $table->id(); $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number'); $table->date('starts_on'); $table->date('ends_on')->nullable();
            $table->decimal('value', 12, 2)->default(0); $table->string('status')->default('active'); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('supplier_rates', function (Blueprint $table) {
            $table->id(); $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('service'); $table->decimal('rate', 12, 2); $table->string('unit')->default('per booking');
            $table->date('effective_from'); $table->date('effective_until')->nullable(); $table->timestamps();
        });
        Schema::create('supplier_availability', function (Blueprint $table) {
            $table->id(); $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->date('available_on'); $table->unsignedInteger('capacity')->default(0); $table->string('status')->default('available');
            $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('supplier_performances', function (Blueprint $table) {
            $table->id(); $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->string('period'); $table->decimal('score', 5, 2)->default(0); $table->unsignedInteger('bookings_completed')->default(0);
            $table->text('notes')->nullable(); $table->timestamps(); $table->unique(['supplier_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_performances'); Schema::dropIfExists('supplier_availability');
        Schema::dropIfExists('supplier_rates'); Schema::dropIfExists('supplier_contracts');
    }
};