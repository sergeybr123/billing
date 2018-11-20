<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdditionalSubscribesTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_subscribes_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');                                                 // наименование типа дополнения
            $table->decimal('price', 7, 2)->default(0.00);       // наименование типа дополнения
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();                            // дата удаления типа дополнения
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_subscribes_types');
    }
}
