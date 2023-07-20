<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAmountServiceAmountType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('description_ar');
            $table->dropColumn('service_type_id');
            $table->dropColumn('amount_service_type');
            $table->dropColumn('embassy_id');
        });
        Schema::table('service_details', function (Blueprint $table) {
            $table->integer('service_type_id')->nullable();
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
        Schema::table('service', function (Blueprint $table) {
            //
        });
    }
}
