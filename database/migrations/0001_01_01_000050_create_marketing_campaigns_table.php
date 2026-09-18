<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('channel', ['email', 'social_media', 'referral', 'ads', 'events']);
            $table->foreignId('tour_package_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('budget', 12, 2)->default(0);
            $table->decimal('actual_spend', 12, 2)->default(0);
            $table->integer('leads_generated')->default(0);
            $table->integer('conversions')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['planned', 'active', 'completed', 'paused'])->default('planned');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('marketing_campaigns'); }
};
