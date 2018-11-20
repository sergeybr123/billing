<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdditionalSubscribesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_subscribes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscribe_id');                          // ид подписки к которой она относится
            $table->foreign('subscribe_id')->references('id')->on('subscribes')->onDelete('cascade');
            $table->unsignedInteger('additional_subscribe_type_id');             // тип дополнительной подписки
            $table->foreign('additional_subscribe_type_id')->references('id')->on('additional_subscribes_types')->onDelete('cascade');
            $table->unsignedInteger('quantity')->default(0);               // дата окончания триальнуй версии подписки
            $table->decimal('price', 7, 2)->default(0.00);    // дата окончания триальнуй версии подписки
            $table->timestamp('trial_ends_at')->nullable();                      // дата окончания триальнуй версии подписки
            $table->timestamp('start_at')->nullable();                           // дата начала подписки
            $table->timestamp('end_at')->nullable();                             // дата окончания подписки
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();                         // дата удаления типа дополнения
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_subscribes');
    }
}
