<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('envoy_settings', function (Blueprint $table) {
            $table->integer('target_inspection_requests')->default(0)->after('target_conversion_rate');
            $table->integer('weight_inspection_requests')->default(0)->after('weight_conversion_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envoy_settings', function (Blueprint $table) {
            $table->dropColumn(['target_inspection_requests', 'weight_inspection_requests']);
        });
    }
};
