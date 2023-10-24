<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;

use Tests\TestCase;

use App\Jobs\Snp2geneProcess;
use App\Http\Controllers\S2GController;
use App\Models\User;
use App\Models\SubmitJob;

class S2GTest extends TestCase
{
    use RefreshDatabase; // it will migrate and seed the database before each test (literally before each function test below)
    protected static $db_inited = false;

    protected static function initDB()
    {
        Artisan::call('migrate:fresh --seed');
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!static::$db_inited) {
            static::$db_inited = true;
            static::initDB();
        }
    }

    public function test_getJobList_with_non_logged_in_user(): void
    {
        $response = $this->get('/snp2gene/getJobList');

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    public function test_getJobList_with_logged_in_user(): void
    {
        $user = User::first();
        $this->actingAs($user);

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

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function test_getNumberScheduledJobs_if_jobs_exist(): void
    {
        $object = App::make(S2GController::class);

        $res = $this->invokeMethod($object, 'getNumberScheduledJobs', array(1));
        $this->assertTrue($res === 4);
    }

    public function test_getNumberScheduledJobs_if_jobs_does_not_exist(): void
    {
        $object = App::make(S2GController::class);

        $res = $this->invokeMethod($object, 'getNumberScheduledJobs', array(100));
        $this->assertTrue($res === 0);
    }

    public function test_getjobIDs_with_non_logged_in_user(): void
    {
        $response = $this->post('/snp2gene/getjobIDs');

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    public function test_getjobIDs_with_logged_in_user_jobs_exist(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->post('/snp2gene/getjobIDs');

        # assert if the response is 200
        $response->assertStatus(200);

        # assert if the response is not an empty json object
        $this->assertTrue(!count($response->json()) == 0);

        # assert if the response is json object
        # assert the response's json structure
        $response->assertJsonStructure([
            '*' => [
                "jobID",
                "title"
            ]
        ]);
    }

    public function test_getjobIDs_with_logged_in_user_jobs_does_not_exist(): void
    {
        $user = User::find(2);
        $this->actingAs($user);

        $response = $this->post('/snp2gene/getjobIDs');

        # assert if the response is 200
        # assert if the response is an empty json object
        $response->assertStatus(200)
            ->assertExactJson([]);
    }

    public function test_getFinishedjobsIDs_with_non_logged_in_user(): void
    {
        $response = $this->post('/snp2gene/getGeneMapIDs');

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    public function test_getFinishedjobsIDs_with_logged_in_user_jobs_exist(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->post('/snp2gene/getGeneMapIDs');

        # assert if the response is 200
        $response->assertStatus(200);

        # assert if the response is not an empty json object
        $this->assertTrue(!count($response->json()) == 0);

        # assert if the response is json object
        # assert the response's json structure
        $response->assertJsonStructure([
            '*' => [
                "jobID",
                "title"
            ]
        ]);
    }

    public function test_getFinishedjobsIDs_with_logged_in_user_jobs_does_not_exist(): void
    {
        $user = User::find(2);
        $this->actingAs($user);

        $response = $this->post('/snp2gene/getGeneMapIDs');

        # assert if the response is 200
        # assert if the response is an empty json object
        $response->assertStatus(200)
            ->assertExactJson([]);
    }

    public function test_loadParams_with_non_logged_in_user(): void
    {
        $response = $this->post('/snp2gene/loadParams');

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    public function test_loadParams_with_logged_in_user_file_exists(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job = $user->jobs->first();

        $response = $this->post('/snp2gene/loadParams', [
            'id' => $job->jobID
        ]);

        # assert if the response is 200
        $response->assertStatus(200);

        # assert if the response is not an empty json object
        $this->assertTrue(!count($response->json()) == 0);

        $response->assertJsonStructure([
            "created_at",
            "title",
            "FUMA",
            "MAGMA",
            "GWAScatalog",
            "ANNOVAR",
            "gwasfile",
            "chrcol",
            "poscol",
            "rsIDcol",
            "pcol",
            "eacol",
            "neacol",
            "orcol",
            "becol",
            "secol",
            "leadSNPsfile",
            "addleadSNPs",
            "regionsfile",
            "N",
            "Ncol",
            "exMHC",
            "MHCopt",
            "extMHC",
            "ensembl",
            "genetype",
            "leadP",
            "gwasP",
            "r2",
            "r2_2",
            "refpanel",
            "pop",
            "MAF",
            "refSNPs",
            "mergeDist",
            "magma",
            "magma_window",
            "magma_exp",
            "posMap",
            "posMapWindowSize",
            "posMapAnnot",
            "posMapCADDth",
            "posMapRDBth",
            "posMapChr15",
            "posMapChr15Max",
            "posMapChr15Meth",
            "posMapAnnoDs",
            "posMapAnnoMeth",
            "eqtlMap",
            "eqtlMaptss",
            "eqtlMapSig",
            "eqtlMapP",
            "eqtlMapCADDth",
            "eqtlMapRDBth",
            "eqtlMapChr15",
            "eqtlMapChr15Max",
            "eqtlMapChr15Meth",
            "eqtlMapAnnoDs",
            "eqtlMapAnnoMeth",
            "ciMap",
            "ciMapBuiltin",
            "ciMapFileN",
            "ciMapFiles",
            "ciMapFDR",
            "ciMapPromWindow",
            "ciMapRoadmap",
            "ciMapEnhFilt",
            "ciMapPromFilt",
            "ciMapCADDth",
            "ciMapRDBth",
            "ciMapChr15",
            "ciMapChr15Max",
            "ciMapChr15Meth",
            "ciMapAnnoDs",
            "ciMapAnnoMeth"
        ]);
    }

    public function test_loadParams_with_logged_in_user_file_does_not_exist(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->post('/snp2gene/loadParams', [
            'id' => 1000000 // non-existing job id
        ]);

        # assert if the response is 200
        $response->assertStatus(200);

        # assert if the response IS an empty json object
        $response->assertExactJson([]);
    }

    public function test_queueNewJobs_how_many_of_temp_got_queued(): void
    {
        Queue::fake();
        Queue::assertNothingPushed();

        $user_id = 1;
        $user = User::find($user_id);
        $this->actingAs($user);

        $newJobs = SubmitJob::where('user_id', $user_id)
            ->wherein('type', ['snp2gene', 'geneMap'])
            ->where('status', 'NEW')
            ->get()
            ->all();
        $numOfNewJobs = count($newJobs);

        $object = App::make(S2GController::class);
        $this->invokeMethod($object, 'queueNewJobs', array());

        Queue::assertPushed(Snp2geneProcess::class, $numOfNewJobs);

        $this->assertDatabaseMissing('SubmitJobs', [
            'user_id' => $user_id,
            'status' => 'NEW'
        ]);

        $this->assertDatabaseHas('SubmitJobs', [
            'user_id' => $user_id,
            'status' => 'QUEUED'
        ]);
    }

    public function test_checkJobStatus_with_non_logged_in_user(): void
    {
        $response = $this->get('/snp2gene/checkJobStatus');

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    public function test_checkJobStatus_with_logged_in_user_job_exists(): void
    {
        $user = User::find(1);
        $this->actingAs($user);
        $job_id = 2;

        $response = $this->get('/snp2gene/checkJobStatus/' . $job_id);
        $response->assertStatus(200);
        $this->assertTrue(in_array($response->getContent(), [
            'QUEUED',
            'NEW',
            'RUNNING',
            'OK'
        ]));
    }

    public function test_checkJobStatus_with_logged_in_user_job_does_not_exist(): void
    {
        $user = User::find(1);
        $this->actingAs($user);
        $job_id = 10000;

        $response = $this->get('/snp2gene/checkJobStatus/' . $job_id);
        $response->assertStatus(200);
        $this->assertEquals($response->getContent(), 'Notfound');
    }

    public function test_getParams_with_non_logged_in_user(): void
    {
        $response = $this->post('/snp2gene/getParams');

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    public function test_getParams_with_logged_in_user_file_exists(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 1;

        $response = $this->post('/snp2gene/getParams', [
            'jobID' => $job_id
        ]);
        $response->assertStatus(200);
    }

    public function test_getParams_with_logged_in_user_files_does_not_exist(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 100;

        $response = $this->post('/snp2gene/getParams', [
            'jobID' => $job_id
        ]);
        $response->assertStatus(200);
        $this->assertEquals($response->getContent(), "");
    }

    public function test_getFilesContents_with_non_logged_in_user(): void
    {
        $response = $this->post('/snp2gene/getFilesContents');

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    public function test_getFilesContents_with_logged_in_user_no_file_provided(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 1;

        $response = $this->post('/snp2gene/getFilesContents', [
            'jobID' => $job_id,
            'fileNames' => []
        ]);

        $response->assertStatus(200);

        # assert if the response IS an empty json object
        $response->assertExactJson([]);
    }

    public function test_getFilesContents_with_logged_in_user_existent_files_provided(): void
    {
        // This function assumes htat the a job folder with id = 1 alreadt exists
        // Ideally, this function should create a dummy job folder and its files and then test it
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 1;

        $response = $this->post('/snp2gene/getFilesContents', [
            'jobID' => $job_id,
            'fileNames' => ['input.snps', 'input.gwas']
        ]);

        $response->assertStatus(200);

        # assert if the response is a json object
        $response->assertJson([]);
    }

    public function test_getFilesContents_with_logged_in_user_non_existent_files_provided(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 1;

        $response = $this->post('/snp2gene/getFilesContents', [
            'jobID' => $job_id,
            'fileNames' => ['test1', 'test2']
        ]);

        $response->assertStatus(200);

        # assert the response's json structure
        $response->assertJsonStructure([
            'test1',
            'test2'
        ]);

        # assert key 1 contains 0 elements
        $response->assertJsonCount(0, 'test1');

        # assert key 2 contains 0 elements
        $response->assertJsonCount(0, 'test2');
    }

    public function test_MAGMA_expPlot(): void
    {
    }

    public function test_getQueueCap(): void
    {
        $object = App::make(S2GController::class);

        $res = $this->invokeMethod($object, 'getQueueCap', array());
        $this->assertTrue($res === config('queue.jobLimits.queue_cap', 10));
    }

    public function test_newJob(): void
    {
    }

    public function test_geneMap(): void
    {
    }

    public function test_Error5(): void
    {
    }

    public function test_deleteJob(): void
    {
    }

    public function test_filedown(): void
    {
    }

    public function test_checkPublish_with_non_logged_in_user(): void
    {
        $response = $this->post('/snp2gene/checkPublish');

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    public function test_checkPublish_with_logged_in_user_all_records_are_null(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 4;

        $response = $this->post('/snp2gene/checkPublish', [
            'id' => $job_id,
        ]);

        $response->assertStatus(200);

        $job = SubmitJob::find($job_id);
        $data = [
            'publish' => $job->is_public,
            'title' => $job->title,
            'g2f' => $job->child->jobID,
            'author' => $job->user->name,
            'email' => $job->user->email,
            'phenotype' => '',
            'publication' => '',
            'publication_link' => '',
            'sumstats_ref' => '',
            'notes' => ''
        ];
        $response->assertJson($data, false);
    }

    public function test_checkPublish_with_logged_in_user_neither_records_is_null(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 5;

        $response = $this->post('/snp2gene/checkPublish', [
            'id' => $job_id,
        ]);

        $response->assertStatus(200);

        $job = SubmitJob::find($job_id);
        $data = [
            'publish' => $job->is_public,
            'title' => $job->title,
            'g2f' => $job->child->jobID,
            'author' => $job->author,
            'email' => $job->publication_email,
            'phenotype' => $job->phenotype,
            'publication' => $job->publication,
            'publication_link' => $job->sumstats_link,
            'sumstats_ref' => $job->sumstats_ref,
            'notes' => $job->notes
        ];
        $response->assertJson($data, false);
    }

    public function test_checkPublish_with_logged_in_user_job_does_not_exist(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 100;

        $response = $this->post('/snp2gene/checkPublish', [
            'id' => $job_id,
        ]);

        $response->assertStatus(500);
    }

    public function test_publish_with_non_logged_in_user(): void
    {
        $response = $this->post('/snp2gene/publish');

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    public function test_publish_with_logged_in_user_job_exists(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 1;

        $title = 'test title';
        $author = 'test author';
        $publication_email = 'test email';
        $phenotype = 'test phenotype';
        $publication = 'test publication';
        $sumstats_link = 'test sumstats_link';
        $sumstats_ref = 'test sumstats_ref';
        $notes = 'test notes';


        $response = $this->post('/snp2gene/publish', [
            'jobID'                 => $job_id,

            'title'                 => $title,
            'author'                => $author,
            'email'                 => $publication_email,
            'phenotype'             => $phenotype,
            'publication'           => $publication,
            'sumstats_link'         => $sumstats_link,
            'sumstats_ref'          => $sumstats_ref,
            'notes'                 => $notes
        ]);

        $response->assertStatus(200);

        $job = SubmitJob::find($job_id);

        $this->assertEquals($title, $job->title);
        $this->assertEquals($author, $job->author);
        $this->assertEquals($publication_email, $job->publication_email);
        $this->assertEquals($phenotype, $job->phenotype);
        $this->assertEquals($publication, $job->publication);
        $this->assertEquals($sumstats_link, $job->sumstats_link);
        $this->assertEquals($sumstats_ref, $job->sumstats_ref);
        $this->assertEquals($notes, $job->notes);
    }

    public function test_publish_with_logged_in_user_job_does_not_exist(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 100;

        $title = 'test title';
        $author = 'test author';
        $publication_email = 'test email';
        $phenotype = 'test phenotype';
        $publication = 'test publication';
        $sumstats_link = 'test sumstats_link';
        $sumstats_ref = 'test sumstats_ref';
        $notes = 'test notes';

        $response = $this->post('/snp2gene/publish', [
            'jobID'                 => $job_id,

            'title'                 => $title,
            'author'                => $author,
            'email'                 => $publication_email,
            'phenotype'             => $phenotype,
            'publication'           => $publication,
            'sumstats_link'         => $sumstats_link,
            'sumstats_ref'          => $sumstats_ref,
            'notes'                 => $notes
        ]);

        $response->assertStatus(200);
        $this->assertNull(SubmitJob::find($job_id));
    }

    public function test_deletePublicRes_with_non_logged_in_user(): void
    {
        $response = $this->post('/snp2gene/deletePublicRes');

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    public function test_deletePublicRes_with_logged_in_user_job_exists(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 1;

        $response = $this->post('/snp2gene/deletePublicRes', [
            'jobID' => $job_id
        ]);

        $response->assertStatus(200);

        $this->assertFalse(boolval(SubmitJob::find($job_id)->is_public));
    }

    public function test_deletePublicRes_with_logged_in_user_job_does_not_exist(): void
    {
        $user = User::find(1);
        $this->actingAs($user);

        $job_id = 100;

        $response = $this->post('/snp2gene/deletePublicRes', [
            'jobID' => $job_id
        ]);

        $response->assertStatus(500);
    }
}
