<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_schedules', function (Blueprint $table) {
            $table->string('destination')->nullable();
            $table->string('traffic_origin')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tour_schedules', function (Blueprint $table) {
            $table->dropColumn(['destination', 'traffic_origin']);
        });
    }
};
