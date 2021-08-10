<?php

namespace Database\Seeders;

use App\Models\Ranks;
use Illuminate\Database\Seeder;

class RanksTableSeeder extends Seeder
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
              "id"=>"1",
              "name"=>"ónix",
              "description"=>"",
              "points"=>"5000",
            ],
            [
              "id"=>"2",
              "name"=>"Cuarzo",
              "description"=>"",
              "points"=>"10000",
            ],
            [
              "id"=>"3",
              "name"=>"Jade",
              "description"=>"",
              "points"=>"25000",
            ],
            [
              "id"=>"4",
              "name"=>"Turquesa",
              "description"=>"",
              "points"=>"40000",
            ],
            [
              "id"=>"5",
              "name"=>"Amatista",
              "description"=>"",
              "points"=>"70000",
            ],
            [
              "id"=>"6",
              "name"=>"Topacio élite",
              "description"=>"Tener dos directos turquesa.",
              "points"=>"110000",
            ],
            [
              "id"=>"7",
              "name"=>"Zafiro",
              "description"=>"Tener dos directos amatista.",
              "points"=>"300000",
            ],
            [
              "id"=>"8",
              "name"=>"Rubí",
              "description"=>"Tener dos directos topacio élite.",
              "points"=>"450000",
            ],
            [
              "id"=>"9",
              "name"=>"Esmeralda",
              "description"=>"Tener dos directos zafiro.",
              "points"=>"700000",
            ],
            [
              "id"=>"10",
              "name"=>"Doble esmeralda",
              "description"=>"Tener dos directos Rubíes.",
              "points"=>"1200000",
            ],
            [
              "id"=>"11",
              "name"=>"Esmeralda imperial",
              "description"=>"Tener dos directos esmeralda.",
              "points"=>"2000000",
            ],
    ];
    foreach ($arrayRank as $rank ) {
        Ranks::create($rank);
    }
    
    }
}
