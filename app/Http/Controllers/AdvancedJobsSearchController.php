<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubmitJob;
use App\Models\User;
use App\CustomClasses\DockerApi\DockerFactory;

class AdvancedJobsSearchController extends Controller
{
    public function index()
    {
        $unique_status = SubmitJob::select('status')->distinct()->pluck('status')->toArray();

        return view('admin.jobsSearchForm', [
            'unique_status' => $unique_status,
            'users' => (session()->has('users')) ? session('users') : NULL,
            'count' => (session()->has('count')) ? session('count') : NULL,
        ]);
    }

    public function search(Request $request)
    {
        $client = new DockerFactory();

        $column_names = [
            'jobID',
            'title',
            'type',
            'created_at',
            'status',
            'user_id',
            'removed_at',
            'removed_by',
        ];

        $with = [
            'user:id,email',
            'removed_by_user:id,email',
        ];

        $validated = $request->validate([
            'email' => 'nullable|email',
            'job_id' => 'nullable|integer',
        ]);

        $status = (isset($request->status)) ? $request->status : NULL;

        $data = $this->search_jobs($column_names, $with, $validated['email'], $validated['job_id'], $status);

        if ($data['jobs'] != NULL) {
            $data['count'] = $data['jobs']->count();

            if ($data['count'] > 10000) {
                $data['err'] = 'Too many jobs found. Please narrow down your search.';
                $data['jobs'] = NULL;
            }
        }

        if ($data['jobs'] != NULL) {
            // this query is a bit complex so bear with me
            $data['users'] = $data['jobs']
                // order all jobs by created_at desc date
                ->orderBy('created_at', 'desc')
                // get the jobs
                ->get()
                // here is the tricky part: we group by after we get the jobs (not in the query but in the collection)
                // this is a bit less efficient compared to grouping in the sql query
                // but it is the only way to get the jobs grouped by user email without using raw sql or joins
                // because we are using the users.email from the relation with the users table 
                // and not the SubmitJobs.email from the jobs table,
                // since the jobs email is going to be deleted at some point
                // so: group the jobs by user email
                ->groupBy('user.email')
                // and then we sort the groups alphabetically by user email
                ->sortKeys();

            // Now fetch the containers' data for each job and add it to the jobs collection
            $data['users']->each(function ($user) use ($client) {
                $user->each(function ($job) use ($client) {
                    $parameters = array(
                        'label' => array(
                            'com.docker.compose.project=laradock-fuma',
                        ),
                        'name' => array(
                            'job-' . $job->jobID . '-',
                        ),
                    );
                    $parameters = 'filters=' . json_encode($parameters);
                    $dockerContainers = $client->dispatchCommand('/var/run/docker.sock', 'GET', '/containers/json', $parameters);

                    $tmp = array();
                    foreach ($dockerContainers as $container) {
                        array_push($tmp, array(
                            'name' => implode(', ', $container['Names']),
                            'status' => $container['Status'],
                            'service_name' => $container['Labels']['com.docker.compose.service'],
                            'state' => $container['State'],
                        ));
                    }
                    $job->containers = $tmp;
                });
            });

            // dd($data['users']);
        }

        if ($data['jobs'] == NULL || $data['users']->isEmpty()) {
            return redirect()->action([AdvancedJobsSearchController::class, 'index'])->withErrors(['err' => $data['err']])->withInput();
        }

        return redirect()->action([AdvancedJobsSearchController::class, 'index'])
            ->with('users', $data['users'])
            ->with('count', $data['count'])
            ->withInput();
    }

    public function containerAction(Request $request)
    {
        $validated = $request->validate([
            'delete' => 'required_without_all:pause,play|in:delete',
            'pause' => 'required_without_all:delete,play|in:pause',
            'play' => 'required_without_all:delete,pause|in:play',
            'container_name' => 'required|string',
        ]);

        $container_name = substr($validated['container_name'], 1);

        $client = new DockerFactory();

        $success_message = '';

        if (isset($validated['delete'])) {
            $dockerContainerRequest = $client->kill($container_name);
            $success_message = 'Container: ' . $container_name . ' is successfully killed!';
        } elseif (isset($validated['pause'])) {
            $dockerContainerRequest = $client->pause($container_name);
            $success_message = 'Container: ' . $container_name . ' is successfully paused!';
        } elseif (isset($validated['play'])) {
            $dockerContainerRequest = $client->unpause($container_name);
            $success_message = 'Container: ' . $container_name . ' is successfully unpaused!';
        }

        // Since we have validated that either one of the above is set, we can use the same code for all of them
        // without checking again if we run a docker command. In any case one of the above senarios will be executed.
        if ($dockerContainerRequest->getCurlError()) {
            // there was an error with the curl request
            // print the curl error message and curl error code 
            $err = 'Curl Error: ' . $dockerContainerRequest->getCurlError() . '   Http Response Code: ' . $dockerContainerRequest->getHttpResponseCode();
            return redirect()->action([AdvancedJobsSearchController::class, 'index'])->withErrors(['err' => $err]);
        }

        if ($dockerContainerRequest->getHttpResponseCode() == "204") {
            // the container was deleted successfully
            // there in no message from docker api
            // print the http response code $dockerContainerRequest->getHttpResponseCode()
            return redirect()->action([AdvancedJobsSearchController::class, 'index'])->with(['status' => $success_message]);
        } else {
            // there is a message from the docker api
            // print the message $dockerContainerRequest->getMessage()
            // print the http response code $dockerContainerRequest->getHttpResponseCode()
            $err = 'Message: ' . $dockerContainerRequest->getMessage() . '   Http Response Code: ' . $dockerContainerRequest->getHttpResponseCode();

            return redirect()->action([AdvancedJobsSearchController::class, 'index'])->withErrors(['err' => $err]);
        }
    }

    /**
     * This function is used to search for jobs based on the parameters provided.
     * The function is called from the search function in this controller.
     * The function returns an array with the following keys:
     *       $jobs:      a query builder object that can be used to get the jobs from the database.
     *       $err:       a string that contains an error message if the search was not successful.
     *       $is_run:    a boolean that indicates if the search was run or not.
     * The function takes the following parameters:
     *       $columns:   an array of strings that contains the names of the columns to be selected from the database.
     *       $mail:      a string that contains the email of the user to search for.
     *       $job_id:    an integer that contains the job id to search for.
     *       $status:    an array of strings that contains the status of the jobs to search for.
     * It first checks if the email provided exists and if it does, it gets the user from the database.
     * It then checks if the columns array is empty and if it is, it returns an error message.
     * It then creates a query builder object and adds the columns to be selected to it.
     * It then checks if the email and job_id are not null and if they are not, it adds the where clauses to the query builder object.
     * It then checks if the email is not null and the job_id is null and if they are not, it adds the where clauses to the query builder object.
     * It then checks if the email and job_id are null and if they are not, it adds the where clauses to the query builder object.
     * It then checks if the status is not null and if it is not, it adds the where clauses to the query builder object.
     * It then checks if the is_run is true which would mean that one of the above senarios executed, 
     * fills the array with the query builder object.
     * It then returns the array will all its constituents.
     * @param array $columns
     * @param string $email
     * @param integer $job_id
     * @param array $status
     * @return array
     */

    private function search_jobs($columns, $with, $email = NULL, $job_id = NULL, $status = NULL): array
    {
        $data = [
            'jobs' => NULL,
            'err' => '',
            'is_run' => false,
        ];

        if ($email != null) {
            $user = User::where('email', $email)->first();
            if (!isset($user)) {
                $data['err'] = 'User not found.';
                return $data;
            }
        }

        if ($columns == NULL || $with == NULL) {
            $data['err'] = 'Something went wrong. Please contact the administrator. Error msg: No columns to search by.';
            return $data;
        }

        $jobs = SubmitJob::select($columns)
            ->with($with);

        if ($email != null && $job_id != null) {
            // search by job_id and validate user's email
            $data['is_run'] = true;
            $jobs->where('user_id', $user->id)
                ->where('jobID', $job_id);
            $data['err'] = 'This job id was not found or does not belong to that user.';
            // dd($jobs, 'search by email and job_id only');

        } elseif ($email != null && $job_id == null) {
            // search by email and narrow down by status
            $data['is_run'] = true;
            if ($status != null) {
                $jobs->where('user_id', $user->id)
                    ->wherein('status', $status);
                $data['err'] = 'No jobs of that status assosiated with that user found.';
                // dd($jobs, 'search by email and narrow down by status');

            } else {
                // search by email only
                $jobs->where('user_id', $user->id);
                $data['err'] = 'No jobs assosiated with that user found.';
                // dd($jobs, 'search by email only');

            }
        } elseif ($email == null && $job_id != null) {
            // search by job_id only
            $data['is_run'] = true;
            $jobs->where('jobID', $job_id);
            $data['err'] = 'No job with that id found.';
            // dd($jobs, 'search by job_id only');

        } elseif ($status != NULL && $email == null && $job_id == null) {
            // search by status only
            $data['is_run'] = true;
            $jobs->wherein('status', $status);
            $data['err'] = 'No jobs found.';
            // dd($jobs, 'search by status only');

        } else {
            $data['err'] = 'Please provide at least one search parameter.';
        }

        if ($data['is_run']) {
            $data['jobs'] = $jobs;
        }
        return $data;
    }
}
