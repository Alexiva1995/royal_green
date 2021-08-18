<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\OrdenPurchaseTableSeeder;
use Database\Seeders\RanksTableSeeder;
use Database\Seeders\LogRanksTableSeeder;
use Database\Seeders\PackagesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UserSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(PackagesTableSeeder::class);
        $this->call(OrdenPurchaseTableSeeder::class);
        $this->call(RanksTableSeeder::class);
        $this->call(LogRanksTableSeeder::class);
        // $this->call(CountryTableSeeder::class);
        // $this->call(CategoriesTableSeeder::class);
        // $this->call(ServicesTableSeeder::class);
        // $this->call(TimeZoneTableSeeder::class);
        // $this->call(AddSaldoTableSeeder::class);
        // $this->call(WalletTableSeeder::class);
    }
}
