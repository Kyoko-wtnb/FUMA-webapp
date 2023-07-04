<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\S2GController;
use Illuminate\Support\Facades\Artisan;

class S2GTest extends TestCase
{
    private $user;
    protected static $db_inited = false;

    protected static function initDB()
    {
        Artisan::call('migrate:fresh --seed --database=mysql_testing');
    }

    protected function setUp(): void
    {
        parent::setUp();
        static::initDB();

        // if (!static::$db_inited) {
        //     static::$db_inited = true;
        // }
    }

    public function test_getJobList_with_non_loged_in_user(): void
    {
        $response = $this->get('/snp2gene/getJobList');

        $response->assertRedirect('login');
    }

    public function test_getJobList_with_loged_in_user(): void
    {
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
        $this->assertTrue($res === 3);
    }

    public function test_getNumberScheduledJobs_if_jobs_does_not_exist(): void
    {
        $object = App::make(S2GController::class);

        $res = $this->invokeMethod($object, 'getNumberScheduledJobs', array(100));
        $this->assertTrue($res === 0);
    }
}
