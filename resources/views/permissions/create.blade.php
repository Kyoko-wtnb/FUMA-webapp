@extends('layouts.master')
@section('title', '| Create Permission')

@section('content')

<div id="page-content-wrapper">
    <div class='col-lg-4 col-lg-offset-4' style="padding-top:50px;">

        <h1><i class='fa fa-key'></i> Add Permission</h1>
        <br>

        {{ html()->form('POST', route('permissions.store'))->open() }}

        <div class="form-group  @error('name') has-error @enderror">
            {{ html()->label('Name')->for('name') }}
            {{ html()->text('name')->placeholder('Name')->class(['form-control', 'is-invalid' => $errors->has('name')]) }}
            @error('name')
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @enderror
        </div><br>
        @if(!$roles->isEmpty()) 
            <h4>Assign Permission to Roles</h4>
            @foreach ($roles as $role)
                {{ html()->label(
                        html()->checkbox('roles[]', old('roles') && in_array($role->name, old('roles')) ? true : false, $role->name)->id('role-'.$role->id) 
                            . "&nbsp;". ucwords($role->name)
                    )->for('role-'.$role->id) 
                }}
                <br>
            @endforeach
        @endif
        <br>
        {{ html()->submit('Add')->class('btn btn-primary') }}
        {{ html()->form()->close() }}

    </div>
</div>

@endsection