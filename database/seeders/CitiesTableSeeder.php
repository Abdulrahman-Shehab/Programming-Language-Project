<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على المحافظات
        $damascus = Governorate::where('name', 'Damascus')->first();
        $aleppo = Governorate::where('name', 'Aleppo')->first();
        $deir_az_zor = Governorate::where('name', 'Deir ez-Zor')->first();
        // إدراج المدن
        $cities = [
            // Damascus
            ['name' => 'Damascus', 'governorate_id' => $damascus->id],
            ['name' => 'Duma', 'governorate_id' => $damascus->id],
            ['name' => 'Hrusta', 'governorate_id' => $damascus->id],

            // Aleppo
            ['name' => 'Aleppo', 'governorate_id' => $aleppo->id],
            ['name' => 'Al Bab', 'governorate_id' => $aleppo->id],
            ['name' => 'Azaz', 'governorate_id' => $aleppo->id],

            // Deir ez-Zor
            ['name' => 'Deir ez-Zor', 'governorate_id' => $deir_az_zor->id],
            ['name' => 'Bukamal', 'governorate_id' => $deir_az_zor->id],


        ];

        foreach ($cities as $city) {
            City::create($city);
        }
    }
}
