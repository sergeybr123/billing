<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('manager_id')->nullable();                  // ид менеджера выставившего счет, 0 - если пользователь сам оплачивает
            $table->unsignedInteger('user_id')->nullable();                     // ид пользователя
            $table->decimal('amount', 7, 2)->default(0.00);  // сумма платежа
            $table->unsignedTinyInteger('type_id')->nullable();                 // тип платежа 1-оплата, 2-пополнение, 3-оплата услуг
            $table->foreign('type_id')->references('id')->on('type_invoices')->onDelete('set null');
            $table->unsignedInteger('plan_id')->nullable();                     // идентификатор плана
            $table->unsignedInteger('service_id')->nullable();                  // идентификатор услуги
            $table->string('description', 500)->nullable();              // описание платежа
            $table->boolean('paid')->default(FALSE);                      // оплачен или нет 0-нет, 1-да
            $table->timestamp('paid_at')->nullable();                           // дата оплаты
            $table->json('options')->nullable();                                // дополнительные опции
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();                        // дата удаления счета
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
