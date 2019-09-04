<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->enum('type', ['plan', 'service', 'bot']);
            $table->string('model')->nullable();
            $table->unsignedInteger('paid_id');
            $table->string('name')->nullable();
            $table->decimal('price', 7, 2)->default(0.00);
            $table->integer('quantity');
            $table->decimal('amount', 7, 2);
            $table->timestamps();

            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_orders');
    }
}
