<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use fuma\Http\Controllers\S2GController;

use fuma\User;


class S2GFormTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        Gate::before(function () {
            return true;
        });
        $admin = User::where('name', "Test Admin")->first();
        if ($admin != null) {
            $admin->delete();
        }
        $this->actingAsAdmin();
    }
 
    /**
     * Check that the snp2gene'it's basic outline and subpages are present
     * Checking subpages via click requires Laravel Dusk with browser functionality
     * @return void 
     * @throws LogicException 
     * @throws PHPUnit_Framework_ExpectationFailedException 
     */
    public function testExistenceOfSNP2GENEPage()
    {
        // Get and check SNP2GENE (new job) page check the page structure
        $this->visit('/snp2gene')
            ->seePageIs('/snp2gene')
            ->see('New Job')
            ->see('Redo gene mapping')
            ->see('My Jobs')
            ->see('1. Upload input files')
            ->see('2. Parameters for lead SNPs and candidate SNPs identification')
            ->see('3-1. Gene Mapping (positional mapping)')
            ->see('3-2. Gene Mapping (eQTL mapping)')
            ->see('3-3. Gene Mapping (3D Chromatin Interaction mapping)')
            ->see('4. Gene types')
            ->see('5. MHC region')
            ->see('6. MAGMA analysis')
            ->see('Submit Job');

    }

    /**
     * Emulate job submission on the S2G controller
     * @return void 
     */
    public function testS2GNewJob()
    {
        $controller = new S2GController();

        $this->assertNotNull($controller);

        $jobList = $controller->getJobList();
        $this->assertNotNull($jobList);
        print($jobList);
        // Using the default Crohns GWAS so no files
        $files = [
            'GWASsummary' => [],
            'leadSNPs' => [],
            'regions' => [],
        ];
        $request = new Request(array(
            'paramsID' => 0,
            'egGWAS' => 'on',
            'N' => 21389,
            'leadP' => 5e-8,
            'gwasP' => 0.05,
            'r2' => 0.6,
            'r2_2' => 0.1,
            'refpanel' => '1KG/Phase3/EUR',
            'refSNPs' => 'Yes',
            'maf' => 0,
            'mergeDist' => 250,
            'posMap' => 'on',
            'posMapWindow' => 10,
            'posMapCADDth' => 12.37,
            'posMapRDBth' => 7,
            'posMapChr15Max' => 7,
            'posMapChr15Meth' => 'any',
            'posMapAnnoMeth' => 'NA',
            'sigeqtlCheck' => 'on',
            'eqtlP' => 1e-3,
            'eqtlMapCADDth' => 12.37,
            'eqtlMapRDBth' => 7,
            'eqtlMapChr15Max' => 7,
            'eqtlMapChr15Meth' => 'any',
            'eqtlMapAnnoMeth' => 'NA',
            'ciFileN' => 0,
            'ciMapFDR' => 1e-6,
            'ciMapPromWindow' => '250-500',
            'ciMapCADDth' => 12.37,
            'ciMapRDBth' => 7,
            'ciMapChr15Max' => 7,
            'ciMapChr15Meth' => 'any',
            'ciMapAnnoMeth' => 'NA',
            'ensembl' => 'v92',
            'genetype' => array('protein_coding'),
            'MHCregion' => 'exMHC',
            'MHCopt' => 'annot',
            'extMHCregion' => '',
            'magma' => 'on',
            'magma_window' => 0,
            'magma_exp' => array('GTEx/v8/gtex_v8_ts_avg_log2TPM', 'GTEx/v8/gtex_v8_ts_general_avg_log2TPM'),
            'NewJobTitle' => 'Crohns Test 1',
            'SubmitNewJob' => 'Submit Job'
        ));
        $request->files->replace($files);
        //$this->request = $request; - will trigger the request on the current page

        $result = $controller->newJob($request); 

        // success is a redirect to the jobsPanel
        //$this->assertStringEndsWith('snp2gene#joblist-panel', $result->getTargetUrl());
        //$this->assertEquals(302, $result->getStatusCode());
        print(var_dump($result));

    }

    function actingAsAdmin()
    {
        $this->actingAs(factory(fuma\User::class)->create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com'
        ]));
    }

    public function tearDown() {
       User::where('name', "Test Admin")->first()->delete();
       parent::tearDown();
     }
}
