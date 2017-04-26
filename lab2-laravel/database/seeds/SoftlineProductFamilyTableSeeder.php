<?php

use Illuminate\Database\Seeder;

class SoftlineProductFamilyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('softline_product_family')->insert(['name' => 'DreamSpark Standart']);
    }
}
