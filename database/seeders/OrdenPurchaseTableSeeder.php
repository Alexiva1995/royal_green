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
        // OrdenPurchases::factory()->count(20)->create();
        $arrayOrden = [
            
            [
                'iduser' => 2, 
                'package_id' => 9,
                'cantidad' => 1, 
                'total' => 25000,
                'monto' => 25000,
                'status' => '0'
            ],
            [
                'iduser' => 3, 
                'package_id' => 10,
                'cantidad' => 1, 
                'total' => 50000,
                'monto' => 50000,
                'status' => '0'
            ],
            [
                'iduser' => 4, 
                'package_id' => 9,
                'cantidad' => 1, 
                'total' => 25000,
                'monto' => 25000,
                'status' => '0'
            ],
            [
                'iduser' => 5, 
                'package_id' => 8,
                'cantidad' => 1, 
                'total' => 10000,
                'monto' => 10000,
                'status' => '0'
            ],
            [
                'iduser' => 6, 
                'package_id' => 7,
                'cantidad' => 1, 
                'total' => 5000,
                'monto' => 5000,
                'status' => '0'
            ],
            [
                'iduser' => 7, 
                'package_id' => 8,
                'cantidad' => 1, 
                'total' => 10000,
                'monto' => 10000,
                'status' => '0'
            ],
        ];

        foreach ($arrayOrden as $users ) {
            OrdenPurchases::create($users);
        }
        
        //
    }
}
