<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('plan_id')->nullable();                         // идентификатор плана
            $table->string('name')->unique();                                       // наименование услуги
            $table->text('description')->nullable();                                // описание услуги(форматированный текст, тэги)
            $table->decimal('price', 7, 2)->default(0.00);       // стоимость
            $table->boolean('active')->default(TRUE);                         // активная, не активная
            $table->timestamp('start_at')->nullable();                              // дата начала действия услуги
            $table->timestamp('end_at')->nullable();                                // дата окончания действия услуги
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();                            // дата удаления услуги
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
