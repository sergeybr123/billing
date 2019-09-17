<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_invoice_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ref_invoice_id');
            $table->string('type'); // 'plan', 'service', 'bot', 'mount_bonus', 'ref'
            $table->unsignedInteger('paid_id')->nullable();
            $table->string('paid_type')->nullable();
            $table->decimal('price', 7, 2)->default(0.00);
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedInteger('discount')->default(0);
            $table->decimal('amount', 7, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('ref_invoice_id')
                ->references('id')
                ->on('ref_invoices')
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
        Schema::dropIfExists('ref_invoice_details');
    }
}
