<?php

namespace App\Http\Livewire;

use App\Models\SubmitJob;
use App\CustomClasees\DockerClient;


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
        $client = new DockerClient('/var/run/docker.sock');

        $tableName = (new SubmitJob())->getTable();
        $column_names = DB::getSchemaBuilder()->getColumnListing($tableName);

        $index = array_search('email', $column_names);
        if ($index !== FALSE) {
            unset($column_names[$index]);
            $column_names = array_values($column_names);
        }

        $columns = implode(', "|", ', $column_names);

        $results = SubmitJob::select('email', DB::raw('GROUP_CONCAT(' . $columns . ' ORDER BY created_at DESC) as columns'))
            ->groupBy('email')
            ->get();

        $this->users = $results->map(function ($item) use ($column_names, $client) {
            // for each user
            $columns = explode(',', $item->columns);
            $columns = array_map(function ($column) use ($column_names, $client) {
                // for each job 
                $tmp = array_combine($column_names, explode('|', $column));

                $parameters = array(
                    'label' => array(
                        'com.docker.compose.project=laradock-fuma',
                    ),
                    'name' => array(
                        'job-'. $tmp['jobID'],
                    ),
                );
                $parameters = 'filters=' . json_encode($parameters);
                $dockerContainers  = $client->dispatchCommand('/containers/json', $parameters);
                
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
