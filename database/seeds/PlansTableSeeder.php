<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->insert([
            'code' => 'unlimited',
            'name' => 'Unlimited',
            'interval' => 'unlimited',
            'sort_order' => 0,
            'on_show' => 0,
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('plans')->insert([
            'code' => 'basic',
            'name' => 'Basic',
            'price' => 1500,
            'interval' => 'month',
            'sort_order' => 0,
            'on_show' => 0,
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('plans')->insert([
            'code' => 'pro',
            'name' => 'Pro',
            'price' => 16200,
            'interval' => 'year',
            'sort_order' => 0,
            'on_show' => 0,
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('plans')->insert([
            'code' => 'free',
            'name' => 'Free',
            'interval' => 'unlimited',
            'sort_order' => 1,
            'on_show' => 1,
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
