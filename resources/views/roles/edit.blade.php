@extends('layouts.master')
@section('title', '| Edit Role')

@section('content')

<div>
    <div class='col-lg-4 col-lg-offset-4'  style="padding-top:50px;">
        <h1><i class='fa fa-key'></i> Edit Role: {{$role->name}}</h1>
        <hr>

        {{ Form::model($role, array('route' => array('roles.update', $role->id), 'method' => 'PUT')) }}

        <div class="form-group @error('name') has-error @enderror">
            {{ Form::label('name', 'Role Name') }}
            {{ Form::text('name', null, array('class' => 'form-control')) }}
            @error('name')
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @enderror
        </div>

        <h5><b>Assign Permissions</b></h5>
        <div class='form-group @error('permissions') has-error @enderror'>
            @foreach ($permissions as $permission)
                {{Form::checkbox('permissions[]',  $permission->id, $role->permissions ) }}
                {{Form::label($permission->name, ucfirst($permission->name)) }}<br>
            @endforeach
            @error('permissions')
                <span class="help-block">
                    <strong>{{ $errors->first('permissions') }}</strong>
                </span>
            @enderror
        </div>
        <br>
        {{ Form::submit('Apply', array('class' => 'btn btn-primary')) }}

        {{ Form::close() }}    
    </div>
</div>

@endsection