<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GraphLegendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('graph_legenda')->insert([
            [
                'color' => '#FC2200',
                'name' => 'Rood',
                'description' => 'Lage score, aandacht vereist',
                'module_level_answer_id' => '2',
            ],
            [
                'color' => '#F88F00',
                'name' => 'Oranje',
                'description' => 'Matige score, verbetering aanbevolen',
                'module_level_answer_id' => '3',
            ],
            [
                'color' => '#F5D000',
                'name' => 'Geel',
                'description' => 'Goede score, ruimte voor verbetering',
                'module_level_answer_id' => '4',
            ],
            [
                'color' => '#38A772',
                'name' => 'Groen',
                'description' => 'Sterke score, presteert goed',
                'module_level_answer_id' => '5',
            ],
            [
                'color' => '#E1D9D1',
                'name' => 'Wit',
                'description' => 'Niet van toepassing',
                'module_level_answer_id' => '1',
            ],
        ]);
    }
}
