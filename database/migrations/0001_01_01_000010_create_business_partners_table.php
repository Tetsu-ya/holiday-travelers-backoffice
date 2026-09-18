<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('business_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['agency', 'corporate', 'affiliate']);
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('region')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->decimal('performance_score', 5, 2)->default(0); // computed by AI service
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('business_partners'); }
};
