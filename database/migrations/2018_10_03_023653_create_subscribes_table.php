<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscribesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_id');                     // ид пользователя
            $table->unsignedInteger('plan_id');                     // ид плана
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->string('interval', 20);                  // интервал действия плана
            $table->timestamp('trial_ends_at')->nullable();         // дата окончания триальнуй версии подписки
            $table->timestamp('start_at')->nullable();              // дата начала подписки
            $table->timestamp('end_at')->nullable();                // дата окончания подписки
            $table->boolean('active')->default(false);        // статус подписки 1-активна, 0-не активна
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();            // дата удаления подписки менеджером
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscribes');
    }
}
