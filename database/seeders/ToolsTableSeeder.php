<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ToolsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tools')->insert([

            'name' => 'Gatk',
            'version' => '1.1',
            'description' => 'test description',
            'command' => 'gatk',
            'license' => 'mit',
            'user_id' => 1,

        ]);
    }
}
