<?php

namespace Database\Seeders;

use App\Models\Form_section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Form_section::query()->delete();
        $sections = [
            [
                'content_id' => 1,
                'description' => 'Les niveau',
            ],
            [
                'content_id' => 2,
                'description' => 'Module niveau',
            ],
        ];

        Form_section::insert($sections);
    }
}
