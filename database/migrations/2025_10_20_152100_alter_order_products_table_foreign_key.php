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
        Schema::table('order_products', function (Blueprint $table) {
            // Drop the existing foreign key constraint on product_id
            $table->dropForeign(['product_id']);

            // Make product_id nullable
            $table->unsignedBigInteger('product_id')->nullable()->change();

            // Recreate the foreign key with ON DELETE SET NULL
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_products', function (Blueprint $table) {
            // Drop the new FK
            $table->dropForeign(['product_id']);

            // Revert product_id to NOT NULL
            $table->unsignedBigInteger('product_id')->nullable(false)->change();

            // Recreate old constraint with CASCADE
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }
};
