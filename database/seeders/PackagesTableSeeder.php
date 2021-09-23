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
              "name"=>"R50",
              "price"=>"50",
            ],
            [
              "id"=>"2",
              "name"=>"R100",
              "price"=>"100",
            ],
            [
              "id"=>"3",
              "name"=>"R250",
              "price"=>"250",
            ],
             [
              "id"=>"4",
              "name"=>"R500",
              "price"=>"500",
            ],
            [
              "id"=>"5",
              "name"=>"RG1000",
              "price"=>"1000",
            ],
            [
              "id"=>"6",
              "name"=>"RG2000",
              "price"=>"2000",
            ],
            [
              "id"=>"7",
              "name"=>"RG5000",
              "price"=>"5000",
            ],
            [
              "id"=>"8",
              "name"=>"RG10000",
              "price"=>"10000",
            ],
            [
              "id"=>"9",
              "name"=>"RG25000",
              "price"=>"25000",
            ],
            [
              "id"=>"10",
              "name"=>"RG50000",
              "price"=>"50000",
            ],
          
    ];
    foreach ($arrayPackage as $package ) {
        Packages::create($package);
    }
    
    }
}
