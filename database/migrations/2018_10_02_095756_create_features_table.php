<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('features', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_group')->default(0);                       // пометка, что это группа
            $table->unsignedInteger('parent_id')->nullable();                    // ид родителя если это потомок
            $table->string('code', 100)->unique();                        // код фичи(латиница, без пробелов)
            $table->string('name', 255);                                  // наименование фичи
            $table->text('description')->nullable();                             // описание фичи(форматированный текст, теги)
            $table->string('interval')->nullable();                              // day, month, year
            $table->tinyInteger('interval_count')->nullable();                   // кол-во раз
            $table->decimal('price', 7, 2)->default(0.00);    // стоимость фичи
            $table->tinyInteger('sort_order');                                   // поле сортировки
            $table->boolean('active')->default(TRUE);                      // активная, неактивная
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();                         // дата удаления фичи
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('features');
    }
}
