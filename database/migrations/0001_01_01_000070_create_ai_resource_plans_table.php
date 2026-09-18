<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_resource_plans', function (Blueprint $table) {
            $table->id();
            $table->enum('plan_type', ['demand_forecast', 'supplier_allocation', 'marketing_budget', 'partner_matching', 'anomaly_alert']);
            $table->string('subject')->nullable(); // e.g. package name, region
            $table->json('input_snapshot')->nullable(); // data fed to the model
            $table->json('recommendation'); // AI output: structured suggestion
            $table->text('summary')->nullable(); // plain-language explanation
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->enum('status', ['generated', 'reviewed', 'applied', 'dismissed'])->default('generated');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ai_resource_plans'); }
};
