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
              "mes_reinicio"=>0,
              "points"=>"5000",
            ],
            [
              "id"=>"2",
              "name"=>"Cuarzo",
              "description"=>"",
              "mes_reinicio"=>0,
              "points"=>"10000",
            ],
            [
              "id"=>"3",
              "name"=>"Jade",
              "description"=>"",
              "mes_reinicio"=>0,
              "points"=>"25000",
            ],
            [
              "id"=>"4",
              "name"=>"Turquesa",
              "description"=>"",
              "mes_reinicio"=>0,
              "points"=>"40000",
            ],
            [
              "id"=>"5",
              "name"=>"Amatista",
              "description"=>"",
              "mes_reinicio"=>0,
              "points"=>"70000",
            ],
            [
              "id"=>"6",
              "name"=>"Topacio élite",
              "description"=>"Tener dos directos turquesa.",
              "mes_reinicio"=>0,
              "points"=>"110000",
            ],
            [
              "id"=>"7",
              "name"=>"Zafiro",
              "description"=>"Tener dos directos amatista.",
              "mes_reinicio"=>0,
              "points"=>"300000",
            ],
            [
              "id"=>"8",
              "name"=>"Rubí",
              "description"=>"Tener dos directos topacio élite.",
              "mes_reinicio"=>0,
              "points"=>"450000",
            ],
            [
              "id"=>"9",
              "name"=>"Esmeralda",
              "description"=>"Tener dos directos zafiro.",
              "mes_reinicio"=>0,
              "points"=>"700000",
            ],
            [
              "id"=>"10",
              "name"=>"Doble esmeralda",
              "description"=>"Tener dos directos Rubíes.",
              "mes_reinicio"=>0,
              "points"=>"1200000",
            ],
            [
              "id"=>"11",
              "name"=>"Esmeralda imperial",
              "description"=>"Tener dos directos esmeralda.",
              "mes_reinicio"=>0,
              "points"=>"2000000",
            ],
    ];
    foreach ($arrayRank as $rank ) {
        Ranks::create($rank);
    }
    
    }
}
