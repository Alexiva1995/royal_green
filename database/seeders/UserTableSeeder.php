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
                'last_name' => 'RY',
                'fullname' => 'Admin RY',
                'username' => 'AdminRY',
                'email' => 'admin@ry.com',
                'password' => Hash::make('123456789'),
                'whatsapp' => '123456789',
                'admin' => '1',
                'referred_id' => 0,
                'binary_id' => '0'
            ],
            
            [
                'name' => 'Test',
                'last_name' => 'RY',
                'fullname' => 'Test RY',
                'username' => 'TestRY',
                'email' => 'test@ry.com',
                'password' => Hash::make('123456789'),
                'whatsapp' => '123456789',
                'referred_id' => 1,
                'binary_id' => 1,
                'binary_side' => 'I',
            ],

            [
                'name' => 'Test',
                'last_name' => 'RY 2',
                'fullname' => 'Test RY 2',
                'username' => 'TestRY2',
                'email' => 'test@ry2.com',
                'password' => Hash::make('123456789'),
                'whatsapp' => '123456789',
                'referred_id' => 2,
                'binary_id' => 2,
                'binary_side' => 'I',
            ],

            [
                'name' => 'Test',
                'last_name' => 'RY 3',
                'fullname' => 'Test RY 3',
                'username' => 'TestRY3',
                'email' => 'test@ry3.com',
                'password' => Hash::make('123456789'),
                'whatsapp' => '123456789',
                'referred_id' => 3,
                'binary_id' => 3,
                'binary_side' => 'D',
            ],

            [
                'name' => 'Test',
                'last_name' => 'RY 4',
                'fullname' => 'Test RY 4',
                'username' => 'TestRY4',
                'email' => 'test@ry4.com',
                'password' => Hash::make('123456789'),
                'whatsapp' => '123456789',
                'referred_id' => 4,
                'binary_id' => 4,
                'binary_side' => 'D',
            ],

            [
                'name' => 'Test',
                'last_name' => 'RY 5',
                'fullname' => 'Test RY 5',
                'username' => 'TestRY5',
                'email' => 'test@ry5.com',
                'password' => Hash::make('123456789'),
                'whatsapp' => '123456789',
                'referred_id' => 5,
                'binary_id' => 5,
                'binary_side' => 'I',
            ],

            [
                'name' => 'Test',
                'last_name' => 'RY 6',
                'fullname' => 'Test RY 6',
                'username' => 'TestRY6',
                'email' => 'test@ry6.com',
                'password' => Hash::make('123456789'),
                'whatsapp' => '123456789',
                'referred_id' => 6,
                'binary_id' => 6,
                'binary_side' => 'D',
            ],

    ];
    foreach ($arrayUsers as $users ) {
        User::create($users);
    }

    }
}
