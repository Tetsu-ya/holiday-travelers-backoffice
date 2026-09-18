<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->cascadeOnDelete();
            $table->date('schedule_date');
            $table->time('starts_at')->nullable();
            $table->time('ends_at')->nullable();
            $table->string('location')->nullable();
            $table->unsignedInteger('capacity')->default(0);
            $table->string('status')->default('planned');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('resource_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_schedule_id')->constrained()->cascadeOnDelete();
            $table->string('resource_type');
            $table->string('resource_name');
            $table->unsignedInteger('quantity')->default(1);
            $table->string('status')->default('reserved');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('assignment_role')->default('tour_coordinator');
            $table->string('status')->default('assigned');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['tour_schedule_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_assignments');
        Schema::dropIfExists('resource_allocations');
        Schema::dropIfExists('tour_schedules');
    }
};