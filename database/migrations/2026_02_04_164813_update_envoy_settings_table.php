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
            $table->renameColumn('target', 'target_sales');
            $table->integer('target_visits')->default(0);
            $table->float('target_retention_rate')->default(0);
            $table->float('target_conversion_rate')->default(0);
            $table->integer('weight_sales')->default(0);
            $table->integer('weight_visits')->default(0);
            $table->integer('weight_retention_rate')->default(0);
            $table->integer('weight_conversion_rate')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envoy_settings', function (Blueprint $table) {
            $table->renameColumn('target_sales', 'target');
            $table->dropColumn([
                'target_visits',
                'target_retention_rate',
                'target_conversion_rate',
                'weight_sales',
                'weight_visits',
                'weight_retention_rate',
                'weight_conversion_rate',
            ]);
        });
    }
};
