<?php

namespace Database\Seeders;

use App\Models\OrdenPurchases;
use Database\Factories\OrdenPurchasesFactory;
use Illuminate\Database\Seeder;

class OrdenPurchaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        OrdenPurchases::factory()->count(20)->create();
    }
}
