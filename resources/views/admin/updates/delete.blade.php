@extends('layouts.master')

@section('stylesheets')
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        table.table td a.edit {
            color: #FFC107;
        }

        table.table td a.delete {
            color: #E34724;
        }

        table.table td a.visibility {
            color: #746966;
        }

        table.table td i {
            font-size: 19px;
        }
    </style>
@endsection

@section('content')
    <div class="container" style="padding-top: 50px;">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-10">
                    <h2>Recent <b>Updates</b></h2>
                </div>
                <div class="col-sm-1">
                    <button type="button" class="btn btn-info add-new"><i class="fa fa-plus"></i> Add New</button>
                </div>
            </div>
        </div>
        <table class="table table-bordered" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th style=" width: 5%;">ID</th>
                    <th style=" width: 10%;">Title</th>
                    <th style=" width: 7%;">Version</th>
                    <th style=" width: 47%;">Descrition</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th>Writer</th>
                    <th style="text-align:center; width: 7%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($updates as $update)
                    <tr>
                        <td>{{ $update['id'] }}</td>
                        <td>{{ $update['title'] }}</td>
                        <td>{{ $update['version'] }}</td>
                        <td style="word-wrap: break-word">{{ $update['description'] }}</td>
                        <td>{{ $update['created_at'] }}</td>
                        <td>{{ $update['updated_at'] }}</td>
                        <td>{{ $update['writer'] }}</td>
                        <td style="text-align:center;">
                            <a href="" class="edit" title="Edit" data-toggle="tooltip"><i class="material-icons">&#xE254;</i></a>
                            <a href="" class="delete" title="Delete" data-toggle="tooltip"><i class="material-icons">&#xE872;</i></a>
                            @if ($update['is_visible'] == 1)
                                <a class="visibility" title="Visible" data-toggle="tooltip"><i class="material-icons">&#xe8f4;</i></a>
                            @else
                                <a class="visibility" title="Invisible" data-toggle="tooltip"><i class="material-icons">&#xe8f5;</i></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    @livewireScripts
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection
