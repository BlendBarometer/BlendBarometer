<?php

namespace Database\Seeders;

use App\Models\ModuleInformationField;
use Illuminate\Database\Seeder;

class ModuleInformationFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModuleInformationField::query()->delete();

        $rows = [
            [
                'key' => 'summary',
                'title' => 'Samenvatting',
                'placeholder' => 'Beschrijf in 5 zinnen waar deze module om gaat',
                'maxlength' => 2000,
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'goals',
                'title' => 'Leeruitkomsten',
                'placeholder' => 'Beschrijf in 5 zinnen wat de leeruitkomsten van deze module zijn',
                'maxlength' => 2000,
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'evaluation',
                'title' => 'Toetsing',
                'placeholder' => 'Beschrijf hoe de toetsing van deze module plaatsvindt',
                'maxlength' => 2000,
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        ModuleInformationField::insert($rows);
    }
}
