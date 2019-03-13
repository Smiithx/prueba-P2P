<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("receipt")->index()->unique();
            $table->string("status");
            $table->string("currency");
            $table->string("authorization");
            $table->decimal("amount",42,2);
            $table->decimal("discount",42,2);
            $table->string("bank");
            $table->dateTime("solved_in");
            $table->unsignedInteger("payment_id")->index();
            $table->foreign('payment_id')->references('id')->on('payments');
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
        Schema::dropIfExists('transactions');
    }
}
