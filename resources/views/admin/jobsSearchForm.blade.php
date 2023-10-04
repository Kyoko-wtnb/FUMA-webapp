@extends('layouts.master')

@section('stylesheets')
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        table.table td button.pause {
            color: #313b33;
            background: none;
            border: none;
            text-decoration: underline;
            cursor: pointer;
        }

        table.table td button.play {
            color: #11c22f;
            background: none;
            border: none;
            text-decoration: underline;
            cursor: pointer;
        }

        table.table td button.delete {
            color: #E34724;
            background: none;
            border: none;
            text-decoration: underline;
            cursor: pointer;
        }

        table.table td i {
            font-size: 19px;
        }
    </style>
@endsection

@section('content')
    <div class="container" style="padding-top: 50px;">

        <div class="table-title">
            <div class="row">
                <div class="col-sm-10">
                    <h2>Advanced Jobs <b>Search</b></h2>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div>
            {{ html()->form('POST', url('admin/search-jobs'))->open() }}

            <h4><b>Status:</b></h4>
            <div class="container">
                <div class="row">
                    @foreach ($unique_status as $key => $status)
                        <div class="col-md-2">
                            <input type="checkbox" id="status{{ $key }}" name="status[{{ $key }}]"
                                value="{{ $status }}" @isset(old('status')[$key]) checked @endisset>
                                    
                            <label for="status{{ $key }}">{{ $status }}</label>
                        </div>
                        @if (($key + 1) % 6 === 0)
                            </div>
                            <div class="row">
                        @endif
                    @endforeach
                </div>
            </div>
            <br><br>

            <h4><b>Search by email or job Id:</b></h4>
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <label for="email">Email:</label>
                        <input type="text" id="email" name="email" placeholder="optional" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="job_id">Job Id:</label>
                        <input type="text" id="job_id" name="job_id" placeholder="optional" value="{{ old('job_id') }}">
                    </div>
                    <div class="col-md-5">
                        <button type="submit" class="btn btn-info" style="float: right;">Search</button>
                    </div>
                </div>
            </div>
            {{ html()->form()->close() }}
        </div>

        <br>
        <hr/>
        <br>

        {{-- -------------------------------- --}}
        
        <div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Search Result Jobs: <tab>
                        @isset($count)
                            <div style="float:right;">
                                <b>{{ $count }}</b> job{{ ($count > 1) ? 's' : '' }} found
                            </div>
                        @endisset
                            {{-- <a id="refreshTable" wire:click="getRunningJobs">
                                <i class="fa fa-refresh"></i>
                            </a> --}}
                    </div>
                </div>
                <div class="panel-body">
                    {{-- <button class="btn btn-default btn-sm" id="deleteJob" name="deleteJob"
                        style="float:right; margin-right:20px;">Delete selected jobs</button> --}}
                    @isset($users)
                        @foreach ($users as $user => $jobs)
                            <p><strong> {{ $user }} </strong></p>
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
                                    @foreach ($jobs as $job)
                                        @if ($job->status == 'OK')
                                            <tr class="bg-success">
                                        @elseif (str_starts_with($job->status, 'ERROR'))
                                            <tr class="bg-danger">
                                        @elseif ($job->status == 'QUEUED')
                                            <tr class="bg-warning">
                                        @else
                                            <tr>
                                        @endif
                                        <td> {{ $job->jobID }} </td>
                                        <td> {{ $job->title }} </td>
                                        <td> {{ $job->type }} </td>
                                        <td> {{ $job->created_at }} </td>
                                        <td> {{ $job->status }} </td>
                                        <td>
                                            @if (empty($job->containers))
                                                No containers
                                            @else
                                                <table class="table">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Service</th>
                                                            <th>Status</th>
                                                            <th>State</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($job->containers as $container)
                                                            <tr>
                                                                <td>{{ $container['name'] }}</td>
                                                                <td>{{ $container['service_name'] }}</td>
                                                                <td>{{ $container['status'] }}</td>
                                                                <td>{{ $container['state'] }}</td>
                                                                <td style="text-align:center;">
                                                                    {{ html()->form('POST', url('admin/search-jobs/action'))->open() }}
                                                                    <input type="hidden" name="container_name" value="{{ $container['name'] }}">
                                                                    @if ($container['state'] == 'running')
                                                                        <button class="pause" title="Pause" name="pause" value= "pause" data-toggle="tooltip"><i
                                                                                class="material-icons">&#xe034;</i></button>
                                                                    @elseif ($container['state'] == 'paused')
                                                                        <button class="play" title="Play" name="play" value= "play" data-toggle="tooltip"><i
                                                                                class="material-icons">&#xe037;</i></button>
                                                                    @endif
                                                                        <button class="delete" title="Delete" name="delete" value= "delete" data-toggle="tooltip"><i
                                                                                class="material-icons">&#xE872;</i></button>
                                                                    {{ html()->form()->close() }}
                                                                </td>
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
                    @else
                        <p align="center">No jobs found</p>
                    @endisset
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection
