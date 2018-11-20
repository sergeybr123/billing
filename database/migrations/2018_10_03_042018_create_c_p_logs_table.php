<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCPLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_p_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('invoice_id')->unsigned()->index();
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->integer('transaction_id')->nullable();
            $table->string('currency', 16)->default('KZT');
            $table->string('cardFirstSix', 6)->nullable();
            $table->string('cardLastFour', 4)->nullable();
            $table->string('cardType')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('issuer')->nullable();
            $table->string('token')->nullable();
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
        Schema::dropIfExists('c_p_logs');
    }
}
