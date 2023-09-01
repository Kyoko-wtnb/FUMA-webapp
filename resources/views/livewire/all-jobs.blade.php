<div>
    <div id="joblist-panel" class="sidePanel container" style="min-height:80vh;">
        <h3>Jobs</h3>
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title">Running Jobs <tab>
                        <a id="refreshTable" wire:click="getRunningJobs">
                            <i class="fa fa-refresh"></i>
                        </a>
                </div>
            </div>
            <div class="panel-body">
                <button class="btn btn-default btn-sm" id="deleteJob" name="deleteJob"
                    style="float:right; margin-right:20px;">Delete selected jobs</button>
                @foreach ($users as $user)
                    <p><strong> {{ $user['email'] }} </strong></p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Job ID</th>
                                <th>Job name</th>
                                <th>Type</th>
                                <th>Submit date</th>
                                <th>Status
                                    <a class="infoPop" data-toggle="popover" data-html="true"
                                        data-content="<b>NEW: </b>The job has been submitted.<br/>
                                        <b>QUEUED</b>: The job has been dispatched to queue.<br/><b>RUNNING</b>: The job is running.<br/>
                                        <b>Go to results</b>: The job has been completed. This is linked to result page.<br/>
                                        <b>ERROR</b>: An error occurred during the process.">
                                        <i class="fa fa-question-circle-o fa-lg"></i>
                                    </a>
                                </th>
                                <th>Containers</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user['jobs'] as $job)
                                @if ($job['status'] == 'OK')
                                    <tr class="bg-success">
                                @elseif ($job['status'] == 'ERROR')
                                    <tr class="bg-danger">
                                @elseif ($job['status'] == 'QUEUED')
                                    <tr class="bg-warning">
                                @else
                                    <tr>
                                @endif
                                <td> {{ $job['jobID'] }} </td>
                                <td> {{ $job['title'] }} </td>
                                <td> {{ $job['type'] }} </td>
                                <td> {{ $job['created_at'] }} </td>
                                <td> {{ $job['status'] }} </td>
                                <td>
                                    @if (empty($job['containers']))
                                        No containers
                                    @else
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Service</th>
                                                    <th>Status</th>
                                                    <th>Service</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($job['containers'] as $container)
                                                    <tr>
                                                        <td>{{ $container['name'] }}</td>
                                                        <td>{{ $container['service_name'] }}</td>
                                                        <td>{{ $container['status'] }}</td>
                                                        <td>{{ $container['state'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </td>
                            @endforeach
                        </tbody>
                    </table>
                    <hr class="bg-danger border-2 border-top border-danger">
                @endforeach
            </div>
        </div>
    </div>
</div>
