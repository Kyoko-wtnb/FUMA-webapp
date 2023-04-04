<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ToolsParametersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tools_parameters')->insert([
            [
                'tool_id' => '1',
                'param_name' => 'i',
                'param_full_name' => 'Input file',
                'level' => 'Basic',
                'group' => 'Input',
                'flag' => '-i',
                'type' => 'test type',
            ],
            [
                'tool_id' => '1',
                'param_name' => 'o',
                'param_full_name' => 'Output file',
                'level' => 'Basic',
                'group' => 'Output',
                'flag' => '-0',
                'type' => 'test type',
            ],
            [
                'tool_id' => '2',
                'param_name' => 'i',
                'param_full_name' => 'Input file',
                'level' => 'Basic',
                'group' => 'Input',
                'flag' => '-i',
                'type' => 'test type',
            ],
            [
                'tool_id' => '2',
                'param_name' => 'o',
                'param_full_name' => 'Output file',
                'level' => 'Basic',
                'group' => 'Output',
                'flag' => '-0',
                'type' => 'test type',
            ],
            [
                'tool_id' => '3',
                'param_name' => 'o',
                'param_full_name' => 'Output file',
                'level' => 'Basic',
                'group' => 'Output',
                'flag' => '-0',
                'type' => 'test type',
            ],
        ]);
    }
}
