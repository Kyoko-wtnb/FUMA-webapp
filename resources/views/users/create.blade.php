@extends('layouts.master')
@section('title', '| Add User')

@section('content')

<div id="page-content-wrapper">
    <div class='col-lg-4 col-lg-offset-4' style="padding-top:50px;">

        <h1><i class='fa fa-user-plus'></i> Add User</h1>
        <hr>

        {{ Form::open(array('route' => 'users.store')) }}

        <div class="form-group @error('name') has-error @enderror">
            {{ Form::label('name', 'Name') }}
            {{ Form::text('name', '', array('class' => 'form-control')) }}
            @error('name')
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group @error('email') has-error @enderror">
            {{ Form::label('email', 'Email') }}
            {{ Form::email('email', '', array('class' => 'form-control')) }}
            @error('email')
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @enderror
        </div>

        <div class='form-group'>
            @foreach ($roles as $role)
                {{ Form::checkbox('roles[]',  $role->id ) }}
                {{ Form::label($role->name, ucfirst($role->name)) }}<br>

            @endforeach
        </div>

        <div class="form-group @error('password') has-error @enderror">
            {{ Form::label('password', 'Password') }}<br>
            {{ Form::password('password', array('class' => 'form-control')) }}
            @error('password')
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group @error('password-confirmation') has-error @enderror">
            {{ Form::label('password', 'Confirm Password') }}<br>
            {{ Form::password('password_confirmation', array('class' => 'form-control')) }}
            @error('password_confirmation')
                <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @enderror
        </div>

        {{ Form::submit('Add', array('class' => 'btn btn-primary')) }}

        {{ Form::close() }}

    </div>
</div>

@endsection