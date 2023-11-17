@extends('layouts.master')
@section('title', '| Edit Role')

@section('content')

<div>
    <div class='col-lg-4 col-lg-offset-4'  style="padding-top:50px;">
        <h1><i class='fa fa-key'></i> Edit Role: {{$role->name}}</h1>
        <hr>

        {{ html()->modelForm($role, 'PUT', route('roles.update', $role->id))->open() }}

        <div class="form-group @error('name') has-error @enderror">
            {{ html()->label('Role name')->for('name') }}
            {{ html()->text('name')->placeholder('Role name')->class(['form-control']) }}
            @error('name')
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @enderror
        </div>

        <h5><b>Assign Permissions</b></h5>
        <div class='form-group @error('permissions') has-error @enderror'>
            @foreach ($permissions as $permission)
                {{ html()->div(
                        html()->label(
                            html()->checkbox('permissions[]', $role->permissions->contains($permission->id), $permission->id)
                            ->id('permission-'.$permission->id)
                                . "&nbsp;" . ucwords($permission->name)
                        )->for('permission-'.$permission->id) . "<br>" 
                    )
                }}
            @endforeach
            @error('permissions')
                <span class="help-block">
                    <strong>{{ $errors->first('permissions') }}</strong>
                </span>
            @enderror
        </div>
        <br>
        {{ html()->submit('Apply', array('class' => 'btn btn-primary')) }}

        {{ html()->closeModelForm() }}    
    </div>
</div>

@endsection