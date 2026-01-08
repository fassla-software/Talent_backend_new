<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('admin_plumber_notifications', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('title'); // Adding subject column after title
        });
    }

    public function down()
    {
        Schema::table('admin_plumber_notifications', function (Blueprint $table) {
            $table->dropColumn('subject'); // Remove column if rolled back
        });
    }
};
