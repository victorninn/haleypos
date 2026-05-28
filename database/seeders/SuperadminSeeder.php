<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@playhq.test'],
            [
                'business_id'   => null,
                'name'          => 'Platform Superadmin',
                'password'      => Hash::make('superadmin'),
                'role'          => 'admin',
                'is_active'     => true,
                'is_superadmin' => true,
            ]
        );
    }
}
