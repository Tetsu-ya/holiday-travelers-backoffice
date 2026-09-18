<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_tasks', function (Blueprint $table) {
            $table->id(); $table->foreignId('assigned_to')->constrained('users')->cascadeOnDelete();
            $table->string('title'); $table->text('description')->nullable(); $table->date('due_date')->nullable();
            $table->string('priority')->default('normal'); $table->string('status')->default('todo'); $table->timestamps();
        });
        Schema::create('staff_schedules', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('schedule_date'); $table->time('starts_at')->nullable(); $table->time('ends_at')->nullable();
            $table->string('location')->nullable(); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('staff_performances', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('period'); $table->decimal('score', 5, 2)->default(0); $table->unsignedInteger('bookings_completed')->default(0);
            $table->decimal('revenue_generated', 12, 2)->default(0); $table->text('notes')->nullable(); $table->timestamps();
            $table->unique(['user_id', 'period']);
        });
        Schema::create('staff_commissions', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('period'); $table->decimal('basis_amount', 12, 2)->default(0); $table->decimal('rate', 5, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0); $table->string('status')->default('pending'); $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_commissions'); Schema::dropIfExists('staff_performances');
        Schema::dropIfExists('staff_schedules'); Schema::dropIfExists('staff_tasks');
    }
};