<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("request_id")->index();
            $table->decimal("amount",42,2);
            $table->string("description");
            $table->string("currency");
            $table->string("status")->nullable();
            $table->dateTime("solved_in")->nullable();
            $table->string("process_url")->nullable();
            $table->string("reference")->index()->unique();
            $table->dateTime("expiry_at");
            $table->dateTime("expired_at")->nullable();
            $table->ipAddress("ip");
            $table->string("user_agent");
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
        Schema::dropIfExists('payments');
    }
}
