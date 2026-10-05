<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('agreement_status')->default('active_contract')->after('status');
            $table->string('perks_inclusions', 500)->nullable()->after('agreement_status');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['agreement_status', 'perks_inclusions']);
        });
    }
};
