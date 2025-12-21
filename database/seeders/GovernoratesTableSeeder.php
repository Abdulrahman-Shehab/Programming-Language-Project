<?php

namespace Database\Seeders;

use App\Models\Governorate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GovernoratesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $governorates = [
            ['name' => 'Damascus'],
            ['name' => 'Aleppo'],
            ['name' => 'Deir ez-Zor'],

        ];

        foreach ($governorates as $governorate) {
            Governorate::create($governorate);
        }
    }
}
