<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('request_id')->nullable();
            $table->string('title')->nullable();
            $table->integer('payment_type_id')->nullable();
            $table->string('amount')->nullable();
            $table->string('paid_at')->nullable();
            $table->string('tax_amount')->nullable();
            $table->string('payment_ref')->nullable();
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
        Schema::dropIfExists('transactions_history');
    }
}
