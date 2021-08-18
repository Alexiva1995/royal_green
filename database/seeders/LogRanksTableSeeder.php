<?php

namespace Database\Seeders;

use App\Models\LogRanks;
use Illuminate\Database\Seeder;

class LogRanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrayRank = [
            
            [
            "iduser"=>1,
            "rank_id"=>1,
            ],
            [
            "iduser"=>1,
            "rank_id"=>2,
              ],
            [
            "iduser"=>1,
            "rank_id"=>3,
            ],
            [
            "iduser"=>2,
            "rank_id"=>1,
            ],
            [
            "iduser"=>2,
            "rank_id"=>2,
            ],
            [
            "iduser"=>2,
            "rank_id"=>4,
            ],
            [
            "iduser"=>3,
            "rank_id"=>1,
            ],
    ];
    foreach ($arrayRank as $rank ) {
        LogRanks::create($rank);
    }
    }
}
