@extends('layouts.master')

@section('title', '| Permissions')

@section('head')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
var loggedin = "{{ Auth::check() }}";
</script>
@stop

@section('content')

<div id="page-content-wrapper">
    <div class="col-lg-10 col-lg-offset-1"  style="padding-top:50px;">
        <h1><i class="fa fa-key"></i>Available Permissions

        <a href="{{ route('users.index') }}" class="btn btn-default pull-right">Users</a>
        <a href="{{ route('roles.index') }}" class="btn btn-default pull-right">Roles</a></h1>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>Permissions</th>
                        <th>Operation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td> 
                        <td>
                        <a href="{{ route('permissions.edit', $permission->id) }}" class="btn btn-info pull-left" style="margin-right: 3px;">Edit</a>

                        {!! Form::open(['method' => 'DELETE', 'route' => ['permissions.destroy', $permission->id] ]) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <a href="{{ route('permissions.create') }}" class="btn btn-success">Add Permission</a>

    </div>
</div>

@endsection