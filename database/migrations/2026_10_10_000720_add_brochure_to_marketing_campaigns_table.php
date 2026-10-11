<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->foreignId('marketing_brochure_id')
                ->nullable()
                ->after('tour_package_id')
                ->constrained('marketing_brochures')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('marketing_campaigns', function (Blueprint $table) {
            $table->dropConstrainedForeignId('marketing_brochure_id');
        });
    }
};
