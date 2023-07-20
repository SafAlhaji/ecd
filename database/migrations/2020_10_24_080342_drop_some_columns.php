<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSomeColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('branches', function (Blueprint $table) {
        //     $table->dropColumn('description');
        //     $table->dropColumn('description_ar');
        //     $table->dropColumn('embassy_id');
        // });
        Schema::table('service_type', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
        Schema::table('service', function (Blueprint $table) {
            $table->string('amount_service_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
