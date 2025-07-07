<?php

namespace Database\Seeders;

use App\Models\Academy;
use Illuminate\Container\Attributes\Database;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Academy::query()->delete();
        $academies = [
            [
                'name' => 'Academie voor Management en Finance',
                'abbreviation' => 'AMF'
            ],
            [
                'name' => 'Academie voor Business en Entrepeneurship',
                'abbreviation' => 'ABE'
            ],
            [
                'name' => 'Academie voor Welzijn, Educatie en Gezondheid',
                'abbreviation' => 'AWEG'
            ],
            [
                'name' => 'Academie voor Technologie en Innovatie',
                'abbreviation' => 'ATIX'
            ],
            [
                'name' => 'Academie voor Technologie en Design',
                'abbreviation' => 'ATD'
            ],
            [
                'name' => 'Academie voor Deeltijd',
                'abbreviation' => 'AVD'
            ],
            [
                'name' => 'Avans Academie Associate Degrees',
                'abbreviation' => 'AAAD'
            ],
            [
                'name' => 'Academie voor Duurzaam Gebouwde Omgeving',
                'abbreviation' => 'ADGO'
            ],
            [
                'name' => 'Academie voor Management, Bestuur en Finance',
                'abbreviation' => 'AMBF'
            ],
            [
                'name' => 'Academie voor Welzijn en Gezondheid',
                'abbreviation' => 'AWG'
            ],
            [
                'name' => 'Academie voor Waardecreatie en Ondernemerschap',
                'abbreviation' => 'AWO'
            ],
            [
                'name' => 'Avans Creative Innovation',
                'abbreviation' => 'ACI'
            ],
            [
                'name' => 'Academie voor Life Sciences en Technologie',
                'abbreviation' => 'ALST'
            ],
        ];

        Academy::insert($academies);
    }
}
