<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 100)->unique();               // код подписки(латиница, без пробелов)
            $table->string('name', 255);                         // наименование подписки
            $table->string('discount', 255)->nullable();         // наименование скидок(если имеются в системе)
            $table->text('description')->nullable();                    // описание(может содержать фарматированный текст, теги)
            $table->decimal('price', 7, 2)->nullable();    // стоимость подписки
            $table->string('interval', 20);                      // период действия - "unlimit"-неограниченно, "month"-месяц, "year"-год
            $table->tinyInteger('trial_period_days')->nullable();       // срок дейиствия триальной версии продукта
            $table->tinyInteger('sort_order');                          // поле для сортировки
            $table->boolean('on_show')->default(TRUE);            // показывать в списке пакетов на сайте
            $table->boolean('active')->default(TRUE);             // активный, неактивный (публикация)
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
        Schema::dropIfExists('plans');
    }
}
