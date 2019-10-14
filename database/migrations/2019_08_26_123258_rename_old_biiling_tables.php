<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameOldBiilingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('cp_logs', 'old_cp_logs');
        Schema::rename('invoices', 'old_invoices');
        Schema::rename('plans', 'old_plans');
        Schema::rename('plan_features', 'old_plan_features');
        Schema::rename('plan_subscriptions', 'old_plan_subscriptions');
        Schema::rename('plan_subscription_usages', 'old_plan_subscription_usages');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
