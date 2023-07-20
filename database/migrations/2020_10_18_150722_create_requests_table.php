<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('branch_id')->nullable();
            $table->string('staff_id')->nullable();
            $table->integer('batch_id')->nullable();
            $table->string('amount')->nullable();
            $table->integer('request_status_id')->nullable();
            $table->integer('embassy_id')->nullable();
            $table->string('qr_string')->nullable();
            $table->string('qr_image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
