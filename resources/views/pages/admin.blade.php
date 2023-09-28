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
        <ul>
            <li><a href="#">Admin</a></li>
            <li><a href="/admin/jobs">Jobs</a></li>
            <li><a href="/admin/search-jobs">Search Jobs</a></li>
            <li><a href="/admin/analysis">Analysis</a></li>
            <li><a href="/admin/updates">Updates</a></li>
        </ul>
    </div>
@endsection

@section('scripts')
    @livewireScripts
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection
