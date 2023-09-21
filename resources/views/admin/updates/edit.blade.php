@extends('layouts.master')

@section('stylesheets')

@endsection

@section('content')
<div class="container" style="padding-top: 50px;">
    <div class="table-title">
        <div class="row">
            <div class="col-sm-10">
                <h2>Edit {{ $id }} update</h2>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @livewireScripts
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection
