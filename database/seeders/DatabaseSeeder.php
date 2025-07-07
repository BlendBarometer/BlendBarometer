<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        $this->call(ContentSeeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(AcademySeeder::class);
        $this->call(FormSectionSeeder::class);
        $this->call(SubCategorySeeder::class);
        $this->call(QuestionCategorySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ModuleLevelAnswerSeeder::class);
        $this->call(GraphDescriptionSeeder::class);
        $this->call(EmailRuleSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(GraphLegendaSeeder::class);

        Schema::enableForeignKeyConstraints();
    }
}
