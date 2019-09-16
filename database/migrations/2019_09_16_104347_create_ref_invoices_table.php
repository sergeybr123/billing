<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('invoice_id')->nullable();               // идентификатор счета
            $table->unsignedInteger('manager_id')->nullable();                  // ид менеджера выставившего счет, 0 - если пользователь сам оплачивает
            $table->unsignedInteger('user_id')->nullable();                     // ид пользователя
            $table->decimal('amount', 7, 2)->default(0.00);  // сумма платежа
            $table->unsignedTinyInteger('type_id')->nullable();                 // тип платежа 1-оплата, 2-пополнение, 3-оплата услуг
            $table->string('description', 500)->nullable();              // описание платежа
            $table->timestamps();

            $table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->onDelete('cascade');
            $table->foreign('type_id')
                ->references('id')
                ->on('type_invoices')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_invoices');
    }
}
