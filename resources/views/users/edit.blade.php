@extends('layouts.master')
@section('title', '| Edit User')

@section('content')

<div id="page-content-wrapper">
    <div class='col-lg-4 col-lg-offset-4' style="padding-top:50px;">

        <h1><i class='fa fa-user-plus'></i> Edit {{$user->name}}</h1>
        <hr>

        {{-- modelForm bind to $user model populate our fields with data using model names --}}
        {{ html()->modelForm($user, 'PUT', route('users.update', $user->id))->open() }}

            {{-- The user model contains: name, email and password --}}
            <div class="form-group @error('name')) has-error @enderror">
                {{ html()->label('Name')->for('name') }}
                {{ html()->text('name')->placeholder('Name')->class(['form-control', 'is-invalid' => $errors->has('name')]) }}
                @error('name'))
                    <span class="help-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group @error('email') has-error @enderror">
                {{ html()->label('Email')->for('email') }}
                {{ html()->text('email')->placeholder('A unique, valid e-mail address')->class(['form-control', 'is-invalid' => $errors->has('email')]) }}
                @error('email')
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @enderror
            </div>

            <h5><b>Assign Role(s)</b></h5>

            {{-- The role model contains: name, permissions --}}
            <div class='form-group'>
                @foreach ($roles as $role)
                    {{ html()->div(
                            html()->label(
                                html()->checkbox('roles[]', $user->roles->contains($role->id), $role->id)
                                ->id('role-'.$role->id) 
                                    . "&nbsp;". ucwords($role->name)
                            )->for('role-'.$role->id) . "<br>"
                        )
                    }}
                @endforeach
            </div>

            <h5><b>Optionally change password</b></h5>
            <div class="form-group @error('password') has-error @enderror }}">
                {{ html()->label('Password')->for('password') }}
                {{ html()->password('password')->placeholder('A valid password')->value('')->class(['form-control']) }}
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group @error('password_confirmation') has-error @enderror }}">
                {{ html()->label('Confirm Password')->for('password_confirmation') }}
                {{ html()->password('password_confirmation')->class(['form-control']) }}
                @if ($errors->has('password_confirmation'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                    </span>
                @endif
            </div>

        {{ html()->submit('Apply', array('class' => 'btn btn-primary')) }}

        {{ html()->closeModelForm() }}

    </div>
</div>

@endsection

@section('scripts')
    {{-- Set the confirm password to be correct by default to allow update to work--}}
    <script type="text/javascript">
        document.getElementById("password_confirmation").value = document.getElementById("password").value;
    </script>
@endsection