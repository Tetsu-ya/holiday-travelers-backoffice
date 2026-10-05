<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->date('agreement_valid_until')->nullable()->after('agreement_status');
        });

        DB::table('suppliers')->whereNull('agreement_valid_until')->update([
            'agreement_valid_until' => now()->addYear()->toDateString(),
        ]);
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('agreement_valid_until');
        });
    }
};
