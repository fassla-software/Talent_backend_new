<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('plumber_received_gifts', function (Blueprint $table) {
        $table->string('status')->default('Pending');
    });
}

public function down()
{
    Schema::table('plumber_received_gifts', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
