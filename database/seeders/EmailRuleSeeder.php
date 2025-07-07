<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EmailRule;
use Illuminate\Database\Seeder;

class EmailRuleSeeder extends Seeder
{
    public function run(): void
    {
        EmailRule::firstOrCreate(
            ['academy_name' => null, 'email' => 'ik.dejong@student.avans.nl']
        );
    }
}