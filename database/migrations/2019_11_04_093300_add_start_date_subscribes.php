<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartDateSubscribes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribes', function (Blueprint $table) {
            $table->timestamp('start_subscribe')->after('trial_ends_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscribes', function (Blueprint $table) {
            $table->dropColumn('start_subscribe');
        });
    }
}
