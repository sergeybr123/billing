<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsageFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usage_features', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');     // ид пользователя
            $table->unsignedInteger('feature_id');  // код фичи
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
            $table->tinyInteger('usage_count');     // кол-во использованных раз
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
        Schema::dropIfExists('usage_features');
    }
}
