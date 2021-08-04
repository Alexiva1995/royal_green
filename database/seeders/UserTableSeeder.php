<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $arrayUsers = [
            
            [
                'name' => 'Admin',
                'last_name' => 'BFX',
                'fullname' => 'Admin BFX',
                'username' => 'AdminBFX',
                'email' => 'admin@bfx.com',
                'password' => Hash::make('123456789'),
                'whatsapp' => '123456789',
                'admin' => '1',
                'referred_id' => 0,
                'binary_id' => '0'
            ],
            
            [
                'name' => 'Test',
                'last_name' => 'BFX',
                'fullname' => 'Test BFX',
                'username' => 'TestBFX',
                'email' => 'test@bfx.com',
                'password' => Hash::make('123456789'),
                'whatsapp' => '123456789',
                'referred_id' => 1,
                'binary_id' => 1,
                'binary_side' => 'I',
            ],

            [
                'name' => 'Test',
                'last_name' => 'BFX 2',
                'fullname' => 'Test BFX 2',
                'username' => 'TestBFX2',
                'email' => 'test@bfx2.com',
                'password' => Hash::make('123456789'),
                'whatsapp' => '123456789',
                'referred_id' => 2,
                'binary_id' => 2,
                'binary_side' => 'I',
            ],

    ];
    foreach ($arrayUsers as $users ) {
        User::create($users);
    }

    }
}
