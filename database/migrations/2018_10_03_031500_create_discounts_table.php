<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code', 100);               // кодовое название скидки
            $table->string('name', 255);               // наименование скидки
            $table->tinyInteger('interval')->nullable();      // интервал на который назначается скидка(например, 3, это на 3 месяца, 4- на 4, и т.д.)
            $table->integer('percent')->nullable();           // проценты скидки
            $table->timestamp('start_at')->nullable();        // дата начала действия скидок(если они акционные)
            $table->timestamp('end_at')->nullable();          // дата окончания действия скидок(если они акционные)
            $table->boolean('active')->default(TRUE);   // активная, не активная
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
        Schema::dropIfExists('discounts');
    }
}
