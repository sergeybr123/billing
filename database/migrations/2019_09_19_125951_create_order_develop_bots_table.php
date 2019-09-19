<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderDevelopBotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_develop_bots', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('subscribe_id');
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('amount');
            $table->timestamp('paid_at');
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
        Schema::dropIfExists('order_develop_bots');
    }
}
