<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->date('expires_on')->nullable()->after('file_path');
            $table->string('status')->default('submitted')->after('expires_on');
        });
        Schema::create('visa_applications', function (Blueprint $table) {
            $table->id(); $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name'); $table->string('country'); $table->string('visa_type');
            $table->date('travel_date')->nullable(); $table->date('submitted_on')->nullable();
            $table->string('status')->default('draft'); $table->text('notes')->nullable(); $table->timestamps();
        });
        Schema::create('visa_requirements', function (Blueprint $table) {
            $table->id(); $table->string('country'); $table->string('visa_type'); $table->string('document_name');
            $table->text('description')->nullable(); $table->boolean('required')->default(true); $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visa_requirements'); Schema::dropIfExists('visa_applications');
        Schema::table('documents', function (Blueprint $table) { $table->dropColumn(['expires_on', 'status']); });
    }
};