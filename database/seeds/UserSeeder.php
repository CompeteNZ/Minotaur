<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use Minotaur\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user = new User;

        $user->name = 'Compete';
        $user->email = 'pete@davisonline.co.nz';
        $user->password = Hash::make('Password123');

        $user->save();
    }
}
