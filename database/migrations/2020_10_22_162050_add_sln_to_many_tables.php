<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlnToManyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('snl')->nullable();
        });
        Schema::table('embessies', function (Blueprint $table) {
            $table->string('snl')->nullable();
        });
        Schema::table('profession', function (Blueprint $table) {
            $table->string('snl')->nullable();
        });
        Schema::table('requests', function (Blueprint $table) {
            $table->string('snl')->nullable();
        });
        Schema::table('service', function (Blueprint $table) {
            $table->string('snl')->nullable();
        });
        Schema::table('service_details', function (Blueprint $table) {
            $table->string('snl')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
