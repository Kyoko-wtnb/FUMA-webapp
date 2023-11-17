@extends('layouts.master')
@section('title', '| Add User')

@section('content')

<div id="page-content-wrapper">
    <div class='col-lg-4 col-lg-offset-4' style="padding-top:50px;">

        <h1><i class='fa fa-user-plus'></i> Add User</h1>
        <hr>

        {{ html()->form('POST', route('users.store'))->open() }}

        <div class="form-group @error('name') has-error @enderror">
            {{ html()->label('Name')->for('name') }}
            {{ html()->text('name')->placeholder('Name')->class(['form-control', 'is-invalid' => $errors->has('name')]) }}
            @error('name')
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group @error('email') has-error @enderror">
            {{ html()->label('Email')->for('email') }}
            {{ html()->text('email')->placeholder('Email')->class(['form-control', 'is-invalid' => $errors->has('email')]) }}
            @error('email')
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @enderror
        </div>

        <div class='form-group'>
            @foreach ($roles as $role)
                {{ html()->label(
                        html()->checkbox('roles[]', false, $role->id)
                        ->id('role-'.$role->id) 
                            . "&nbsp;". ucwords($role->name)
                    )->for('role-'.$role->id) 
                }}
                <br>
            @endforeach
        </div>

        <div class="form-group @error('password') has-error @enderror">
            {{ html()->label('Password')->for('password') }}
            {{ html()->password('password')->placeholder('A valid password')->class(['form-control', 'is-invalid' => $errors->has('password')]) }}
            @error('password')
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group @error('password-confirmation') has-error @enderror">
            {{ html()->label('Confirm Password')->for('password_confirmation') }}
            {{ html()->password('password_confirmation')->class(['form-control', 'is-invalid' => $errors->has('password')]) }}
            @error('password_confirmation')
                <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @enderror
        </div>

        {{ html()->submit('Add')->class('btn btn-primary') }}
        {{ html()->form()->close() }}

    </div>
</div>

@endsection