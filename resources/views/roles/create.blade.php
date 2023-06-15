@extends('layouts.master')

@section('title', '| Add Role')

@section('content')

<div id="page-content-wrapper">
    <div class='col-lg-4 col-lg-offset-4' style="padding-top:50px;">

        <h1><i class='fa fa-key'></i> Add Role</h1>
        <hr>

        {{ Form::open(array('route' => 'roles.store')) }}

        <div class="form-group @error('name') has-error @enderror">
            {{ Form::label('name', 'Name') }}
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
                {{ Form::checkbox('permissions[]',  $permission->id ) }}
                {{ Form::label($permission->name, ucfirst($permission->name)) }}<br>
            @endforeach
            @error('permissions')
                <span class="help-block">
                    <strong>{{ $errors->first('permissions') }}</strong>
                </span>
            @enderror
        </div>

        {{ Form::submit('Add', array('class' => 'btn btn-primary')) }}

        {{ Form::close() }}

    </div>
</div>

@endsection