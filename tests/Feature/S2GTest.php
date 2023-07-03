<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use Database\Seeders\SubmitJobsTableSeeder;
use Database\Seeders\UsersTableSeeder;

class S2GTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_getJobList_with_non_loged_in_user(): void
    {
        $response = $this->get('/snp2gene/getJobList');

        $response->assertRedirect('login');
    }

    public function test_getJobList_with_loged_in_user(): void
    {
        $this->seed([
            UsersTableSeeder::class,
            SubmitJobsTableSeeder::class,
        ]);

        $this->user = User::first();
        $this->actingAs($this->user);

        $response = $this->get('/snp2gene/getJobList');

        # assert if the response is json object
        $response->assertJson([]);

        # assert the response's json structure
        $response->assertJsonStructure([
            '*' => [
                "jobID",
                "email",
                "title",
                "created_at",
                "updated_at",
                "status",
                "user_id",
                "type",
                "started_at",
                "completed_at",
                "parent_id",
                "uuid",
                "is_public",
                "author",
                "publication_email",
                "phenotype",
                "publication",
                "sumstats_link",
                "sumstats_ref",
                "notes",
                "published_at"
            ]
        ]);

        # assert the response's json type variable contains only 'snp2gene' and 'geneMap'
        $data = array_column($response->json(), 'type');
        $data = array_unique($data);
        $this->assertTrue(empty(array_diff($data, ['snp2gene', 'geneMap'])));
    }
}
