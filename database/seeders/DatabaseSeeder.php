<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Child;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::firstOrCreate(
            ['slug' => 'haleys-hq'],
            [
                'name' => 'Haleys Playhouse',
                'code' => 'HAL',
                'phone' => '+91 90000 00000',
                'email' => 'hello@haleys.test',
                'address' => '1st Floor, Sunshine Plaza, MG Road',
                'currency_symbol' => '₹',
            ]
        );

        User::firstOrCreate(
            ['email' => 'admin@haleys.test'],
            [
                'business_id' => $business->id,
                'name' => 'Haleys Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@haleys.test'],
            [
                'business_id' => $business->id,
                'name' => 'Front Desk',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_active' => true,
            ]
        );

        $packages = [
            ['name' => '1 Hour Play',  'duration_minutes' => 60,  'price' => 200, 'color' => '#6366f1', 'sort_order' => 1],
            ['name' => '2 Hour Play',  'duration_minutes' => 120, 'price' => 350, 'color' => '#22c55e', 'sort_order' => 2],
            ['name' => '3 Hour Play',  'duration_minutes' => 180, 'price' => 500, 'color' => '#f97316', 'sort_order' => 3],
            ['name' => 'Day Pass',     'duration_minutes' => null,'price' => 800, 'color' => '#ec4899', 'is_unlimited' => true, 'sort_order' => 4],
        ];
        foreach ($packages as $p) {
            Package::firstOrCreate(
                ['business_id' => $business->id, 'name' => $p['name']],
                $p
            );
        }

        $kids = [
            ['name' => 'Aarav Sharma',  'age' => 5, 'gender' => 'male',   'guardian_name' => 'Rohit Sharma',  'contact_number' => '9876500001'],
            ['name' => 'Diya Mehta',    'age' => 4, 'gender' => 'female', 'guardian_name' => 'Neha Mehta',    'contact_number' => '9876500002'],
            ['name' => 'Kabir Iyer',    'age' => 6, 'gender' => 'male',   'guardian_name' => 'Suresh Iyer',   'contact_number' => '9876500003'],
            ['name' => 'Ira Kapoor',    'age' => 3, 'gender' => 'female', 'guardian_name' => 'Pooja Kapoor',  'contact_number' => '9876500004'],
        ];
        foreach ($kids as $k) {
            Child::firstOrCreate(
                ['business_id' => $business->id, 'name' => $k['name']],
                array_merge($k, [
                    'child_code' => 'C-'.strtoupper(Str::random(6)),
                    'emergency_contact' => $k['contact_number'],
                ])
            );
        }
    }
}
