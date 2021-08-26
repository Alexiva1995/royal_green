<?php

namespace Database\Seeders;

use App\Models\Packages;
use App\Models\OrdenPurchases;
use Illuminate\Database\Seeder;

class PackagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayPackage = [
            
            [
              "id"=>"1",
              "name"=>"r50",
              "price"=>"50",
            ],
            [
              "id"=>"2",
              "name"=>"r100",
              "price"=>"100",
            ],
            [
              "id"=>"3",
              "name"=>"r250",
              "price"=>"250",
            ],
             [
              "id"=>"4",
              "name"=>"r500",
              "price"=>"500",
            ],
            [
              "id"=>"5",
              "name"=>"rg1000",
              "price"=>"1000",
            ],
            [
              "id"=>"6",
              "name"=>"rg2000",
              "price"=>"2000",
            ],
            [
              "id"=>"7",
              "name"=>"rg5000",
              "price"=>"5000",
            ],
            [
              "id"=>"8",
              "name"=>"rg10000",
              "price"=>"10000",
            ],
            [
              "id"=>"9",
              "name"=>"rg25000",
              "price"=>"25000",
            ],
            [
              "id"=>"10",
              "name"=>"rg50000",
              "price"=>"50000",
            ],
          
    ];
    foreach ($arrayPackage as $package ) {
        Packages::create($package);
    }
    
    }
}
