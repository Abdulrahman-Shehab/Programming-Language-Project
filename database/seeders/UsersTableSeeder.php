<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'أحمد',
                'last_name' => 'محمد',
                'phone' => '0912345678',
                'birth_date' => '1990-01-01',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'سارة',
                'last_name' => 'علي',
                'phone' => '0987654321',
                'birth_date' => '1992-05-15',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'محمد',
                'last_name' => 'حسن',
                'phone' => '0911223344',
                'birth_date' => '1988-12-10',
                'password' => Hash::make('password123'),
            ],
            [
                'first_name' => 'ليلى',
                'last_name' => 'عبدالله',
                'phone' => '0955667788',
                'birth_date' => '1995-08-20',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
