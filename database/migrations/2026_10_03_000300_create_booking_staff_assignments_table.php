<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_staff_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('assignment_role', 100);
            $table->string('status', 30)->default('assigned');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['booking_id', 'user_id', 'assignment_role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_staff_assignments');
    }
};
