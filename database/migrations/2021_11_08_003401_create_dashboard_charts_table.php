<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDashboardChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dashboard_charts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_user_id')->nullable();
            $table->integer('chart_type')->comment('1 = requests , 2 branchs')->nullable();
            $table->string('title')->nullable();
            $table->text('counts')->nullable();
            $table->string('chart_color')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dashboard_charts');
    }
}
