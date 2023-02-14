@extends('layouts.master')

@section('title', '| Roles')

@section('head')
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript">
var loggedin = "{{ Auth::check() }}";
</script>
@stop

@section('content')

<div>
    <div class="col-lg-10 col-lg-offset-1"  style="padding-top:50px;">
        <h1><i class="fa fa-key"></i> Roles

        <a href="{{ route('users.index') }}" class="btn btn-default pull-right">Users</a>
        <a href="{{ route('permissions.index') }}" class="btn btn-default pull-right">Permissions</a></h1>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Operation</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($roles as $role)
                    <tr>

                        <td>{{ $role->name }}</td>

                        <td>{{ str_replace(array('[',']','"'),'', $role->permissions()->pluck('name')) }}</td>{{-- Retrieve array of permissions associated to a role and convert to string --}}
                        <td>
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-info pull-left" style="margin-right: 3px;">Edit</a>

                        {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id] ]) !!}
                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                        {!! Form::close() !!}

                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        <a href="{{ route('roles.create')  }}" class="btn btn-success">Add Role</a>

    </div>
</div>

@endsection