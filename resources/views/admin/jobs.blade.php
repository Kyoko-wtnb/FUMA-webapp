@extends('layouts.master')

@section('stylesheets')
    @livewireStyles
@endsection

@section('content')
    <div class="container" style="padding-top:50px;">
        <livewire:all-jobs />
    </div>
@endsection

@section('scripts')
    @livewireScripts
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection
