<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\User;
use App\Models\Governorate;
use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على المستخدمين
        $users = User::all();

        // التأكد من وجود مستخدمين
        if ($users->isEmpty()) {
            echo "No users found. Please run UsersTableSeeder first.\n";
            return;
        }

        // الحصول على المحافظات والمدن
        $damascus = Governorate::where('name', 'Damascus')->first();
        $aleppo = Governorate::where('name', 'Aleppo')->first();
        $deir_az_zor = Governorate::where('name', 'Deir ez-Zor')->first();

        // الحصول على المدن
        $damascusCity = City::where('name', 'Damascus')->where('governorate_id', $damascus->id)->first();
        $aleppoCity = City::where('name', 'Aleppo')->where('governorate_id', $aleppo->id)->first();
        $deir_az_zorCity = City::where('name', 'Deir ez-Zor')->where('governorate_id', $deir_az_zor->id)->first();

        $apartments = [
            [
                'user_id' => $users->random()->id,
                'governorate_id' => $damascus->id,
                'city_id' => $damascusCity->id,
                'title' => 'شقة مطلة على الجنة',
                'area' => 95.00,
                'description' => 'شقة مريحة بتصميم عصري ومطلة على الحديقة في منطقة高档 من دمشق',
                'daily_price' => 60000,
                'address' => 'الجنة, دمشق',
            ],
            [
                'user_id' => $users->random()->id,
                'governorate_id' => $aleppo->id,
                'city_id' => $aleppoCity->id,
                'title' => 'شقة تاريخية في حلب',
                'area' => 150.00,
                'description' => 'شقة تاريخية في الأبواب القديمة من حلب مع زخارف أثرية وتصميم أصيل',
                'daily_price' => 85000,
                'address' => 'الأبواب, حلب',
            ],
            [
                'user_id' => $users->random()->id,
                'governorate_id' => $damascus->id,
                'city_id' => $damascusCity->id,
                'title' => 'استوديو حديث في المزة',
                'area' => 60.00,
                'description' => 'استوديو حديث ومريح في منطقة المزة مع مطبخ صغير وحمام جديد',
                'daily_price' => 45000,
                'address' => 'المزة, دمشق',
            ],
            [
                'user_id' => $users->random()->id,
                'governorate_id' => $aleppo->id,
                'city_id' => $aleppoCity->id,
                'title' => 'شقة في الجامع الجديد',
                'area' => 130.50,
                'description' => 'شقة كبيرة بالقرب من الجامع الجديد في حلب مع موقف سيارات داخلي',
                'daily_price' => 80000,
                'address' => 'الجامع الجديد, حلب',
            ],
            [
                'user_id' => $users->random()->id,
                'governorate_id' => $deir_az_zor->id,
                'city_id' => $deir_az_zorCity->id,
                'title' => 'شقة في منطقة الجورة',
                'area' => 120.00,
                'description' => 'شقة كبيرة في منطقة الجورة مع موقف سيارات وحدائق صغيرة',
                'daily_price' => 90000,
                'address' => 'الجورة, دير الزور',
            ],
        ];

        foreach ($apartments as $apartmentData) {
            // التحقق مما إذا كانت الشقة موجودة بالفعل بنفس العنوان والمستخدم
            $existingApartment = Apartment::where('title', $apartmentData['title'])
                ->where('user_id', $apartmentData['user_id'])
                ->first();

            if (!$existingApartment) {
                Apartment::create($apartmentData);
            }
        }

        echo "Created " . count($apartments) . " apartments.\n";
    }
}
