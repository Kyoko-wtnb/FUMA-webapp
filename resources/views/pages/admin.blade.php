@extends('layouts.master')

@section('stylesheets')
    @livewireStyles
@endsection

@section('content')
    <div class="container" style="padding-top:50px;">
        <div style="text-align: center;">
            <h2>Admin Dashboard</h2>
            <h2>This is Admin's page</h2>
        </div>
        <livewire:all-jobs /> 
    </div>
@endsection

@section('scripts')
    @livewireScripts
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection
