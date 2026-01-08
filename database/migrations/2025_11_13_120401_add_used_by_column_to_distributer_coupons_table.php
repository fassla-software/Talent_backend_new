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
        Schema::table('distributor_coupons', function (Blueprint $table) {
            //
            $table->foreignId('used_by')->nullable()->constrained('users')->after('distributor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distributer_coupons', function (Blueprint $table) {
            //
            $table->dropForeign(['used_by']);
        });
    }
};
