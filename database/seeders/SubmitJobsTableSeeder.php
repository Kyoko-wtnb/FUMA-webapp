<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class SubmitJobsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('SubmitJobs')->insert([
            [
                'email' => 'test@gmail.com',
                'title' => 'test1',
                'status' => 'QUEUED',
                'user_id' => 1,
                'type' => 'snp2gene',
                'started_at' => null,
                'completed_at' => null,
                'parent_id' => null,
                'uuid' => null,
                'is_public' => true,
                'author' => 'Dimitri',
                'publication_email' => 'test@gmail.com',
                'phenotype' => null,
                'publication' => null,
                'sumstats_link' => null,
                'sumstats_ref' => null,
                'notes' => null,
                'published_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'email' => 'test@gmail.com',
                'title' => 'test2',
                'status' => 'RUNNING',
                'user_id' => 1,
                'type' => 'geneMap',
                'started_at' => null,
                'completed_at' => null,
                'parent_id' => null,
                'uuid' => null,
                'is_public' => false,
                'author' => 'Dimitri',
                'publication_email' => 'test@gmail.com',
                'phenotype' => null,
                'publication' => null,
                'sumstats_link' => null,
                'sumstats_ref' => null,
                'notes' => null,
                'published_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'email' => 'test@gmail.com',
                'title' => 'test3',
                'status' => 'NEW',
                'user_id' => 1,
                'type' => 'snp2gene',
                'started_at' => null,
                'completed_at' => null,
                'parent_id' => null,
                'uuid' => null,
                'is_public' => false,
                'author' => 'Dimitri',
                'publication_email' => 'test@gmail.com',
                'phenotype' => null,
                'publication' => null,
                'sumstats_link' => null,
                'sumstats_ref' => null,
                'notes' => null,
                'published_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'email' => 'test@gmail.com',
                'title' => 'test4',
                'status' => 'OK',
                'user_id' => 1,
                'type' => 'snp2gene',
                'started_at' => null,
                'completed_at' => null,
                'parent_id' => null,
                'uuid' => null,
                'is_public' => false,
                'author' => null,
                'publication_email' => null,
                'phenotype' => null,
                'publication' => null,
                'sumstats_link' => null,
                'sumstats_ref' => null,
                'notes' => null,
                'published_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'email' => 'test@gmail.com',
                'title' => 'test5',
                'status' => 'NEW',
                'user_id' => 1,
                'type' => 'snp2gene',
                'started_at' => null,
                'completed_at' => null,
                'parent_id' => null,
                'uuid' => null,
                'is_public' => true,
                'author' => 'test author',
                'publication_email' => 'test publication email',
                'phenotype' => 'test phenotype',
                'publication' => 'test publication',
                'sumstats_link' => 'test link',
                'sumstats_ref' => 'test ref',
                'notes' => 'test notes',
                'published_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
