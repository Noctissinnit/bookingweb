<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Alvin Dimas',
            'nis' => '111111',
            'email' => 'alvin.dimas.praditya@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Noctis Yoru',
            'nis' => '987654',
            'email' => 'ncts.yoru@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        User::create([
            'name' => 'Anjing Sedboi',
            'nis' => '123456',
            'email' => 'bimosatriaji6@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'user'
        ]);

      
    }
}
