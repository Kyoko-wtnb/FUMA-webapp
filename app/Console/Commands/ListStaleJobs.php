<?php

namespace fuma\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use fuma\SubmitJob;

class ListStaleJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:liststale {olderthanhours}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List jobs that were QUEUED more than a given number of hours ago but are not yet executed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
	$staledate = Carbon::now()->subHours($this->argument('olderthanhours'));
	$results = SubmitJob::where('status', 'QUEUED')->where('created_at', '<', $staledate)->orderBy('created_at', 'asc')->get();
	$this->info("Stale jobs queued for more than " . $this->argument('olderthanhours') . " hours.\n");
	foreach ($results as $job) {
		$this->info($job);
	}
    }
}
