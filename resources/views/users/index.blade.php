@extends('layouts.master')

@section('title', '| Users')

@section('head')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
var loggedin = "{{ Auth::check() }}";
</script>
@stop

@section('content')

<div id="page-content-wrapper">
    <div class="col-lg-10 col-lg-offset-1" style="padding-top:50px;">
        <h1><i class="fa fa-users"></i> User Role Administration <a href="{{ route('roles.index') }}" class="btn btn-default pull-right">Roles</a>
        <a href="{{ route('permissions.index') }}" class="btn btn-default pull-right">Permissions</a></h1>
        <hr>
        <p>Type something in the input field to search the table for names, emails or roles:</p>  
        <input class="form-control" id="userRoleSearch" type="text" placeholder="Search..">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date/Time Added</th>
                        <th>User Roles</th>
                        <th>Operations</th>
                    </tr>
                </thead>

                <tbody id="userRoleTable">
                    @foreach ($users as $user)
                    <tr>

                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at->format('F d, Y h:ia') }}</td>
                        <td>{{  $user->roles()->pluck('name')->implode(' ') }}</td>{{-- Retrieve array of roles associated to a user and convert to string --}}
                        <td>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info pull-left" style="margin-right: 3px;">Edit</a>

                        {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id] ]) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}

                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
            <script>
            var params = { sortable: true };
            $(document).ready(function(){
                $("#userRoleSearch").on("keyup", function() {
                    var value = $(this).val().toLowerCase();
                    $("#userRoleTable tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
            });
            </script>
        </div>

        <a href="{{ route('users.create') }}" class="btn btn-success" data-toggle="tooltip" title="Usually users are added by registration but can be also be added here.">Add User</a>

    </div>
</div>

@endsection