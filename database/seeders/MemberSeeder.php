<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Member;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Member::insert([
            [
                'name' => 'Anjing Sedboi',
                'email' => 'anjingsedboi@gmail.com'
            ],
            [
                'name' => 'Noctis Yoru',
                'email' => 'ncts.yoru@gmail.com'
            ]
        ]);
    }
}
