<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans_features', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('plan_id');     // ид плана
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->unsignedInteger('feature_id');  // ид фичи
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade');
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
        Schema::dropIfExists('plans_features');
    }
}
