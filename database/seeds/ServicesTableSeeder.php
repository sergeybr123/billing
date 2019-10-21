<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services')->insert([
            'name' => 'Разработка авточата',
            'discount' => 0,
            'price' => 15000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('services')->insert([
            'name' => 'Дополнительный авточата',
            'discount' => 0,
            'discount_option' => ['type' => 'plan', 'value' => 'discount'],
            'price' => 500,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
