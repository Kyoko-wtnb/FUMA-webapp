<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Facades\Hash;


class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Marko',
                'email' => 'test@gmail.com',
                'password' => Hash::make('12345678'),
            ],
            [
                'name' => 'Dimitri',
                'email' => 'test1@gmail.com',
                'password' => Hash::make('12345678'),
            ]
        ]);
    }
}
