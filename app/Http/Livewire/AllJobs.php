<?php

namespace App\Http\Livewire;

use App\Models\SubmitJob;
use App\CustomClasses\DockerApi\DockerFactory;


use Livewire\Component;
use Illuminate\Support\Facades\DB;


class AllJobs extends Component
{
    public $users;

    public function render()
    {
        return view('livewire.all-jobs');
    }

    public function mount()
    {
        $this->getRunningJobs();
    }

    public function getRunningJobs()
    {
        $client = new DockerFactory();

        $column_names = [
            'jobID',
            'title',
            'type',
            'created_at',
            'status',
        ];

        $separator = chr(29); // Specify your desired separator here
        
        $escaped_columns = array_map(function ($columnName) use ($separator) {
            return "REPLACE(`$columnName`, ',', ' ')"; // Replace commas with space
        }, $column_names);

        $column_list = implode(", '$separator', ", $escaped_columns);

        $results = SubmitJob::select('email', DB::raw("GROUP_CONCAT($column_list ORDER BY created_at DESC) as columns"))
            ->whereNotIn('status', ['OK', 'ERROR', 'JOB FAILED', 'QUEUED'])
            ->groupBy('email')
            ->orderByRaw('MAX(CASE WHEN status = "RUNNING" THEN 1 ELSE 0 END) DESC')
            ->get();

        $this->users = $results->map(function ($item) use ($column_names, $client) {
            // for each user
            $columns = explode(',', $item->columns);
            $columns = array_map(function ($column) use ($column_names, $client) {
                // for each job 
                $tmp = array_combine($column_names, explode(chr(29), $column));

                $parameters = array(
                    'label' => array(
                        'com.docker.compose.project=laradock-fuma',
                    ),
                    'name' => array(
                        'job-' . $tmp['jobID'] . '-',
                    ),
                );
                $parameters = 'filters=' . json_encode($parameters);
                $dockerContainers = $client->dispatchCommand('/var/run/docker.sock', 'GET', '/containers/json', $parameters);

                $tmp['containers'] = array();

                foreach ($dockerContainers as $container) {
                    array_push($tmp['containers'], array(
                        'name' => implode(', ', $container['Names']),
                        'status' => $container['Status'],
                        'service_name' => $container['Labels']['com.docker.compose.service'],
                        'state' => $container['State'],
                    ));
                }
                return $tmp;
            }, $columns);

            return [
                'email' => $item->email,
                'jobs' => $columns,
            ];
        });
    }
}
