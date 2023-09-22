@extends('layouts.master')

@section('stylesheets')
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <style>
        table.table td a.edit {
            color: #FFC107;
        }

        table.table td button.delete {
            color: #E34724;
            background: none;
            border: none;
            text-decoration: underline;
            cursor: pointer;
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
                    <a href="{{ url('admin/updates/create') }}" type="button" class="btn btn-info add-new"><i
                            class="fa fa-plus"></i> Add New</a>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <table class="table table-bordered" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th style=" width: 3%;">ID</th>
                    <th style=" width: 10%;">Title</th>
                    <th style=" width: 7%;">Version</th>
                    <th style=" width: 49%;">Descrition</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th>Writer</th>
                    <th style="text-align:center; width: 7%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($updates as $update)
                    <tr style="word-wrap: break-word">
                        <td>{{ $update['id'] }}</td>
                        <td>{{ $update['title'] }}</td>
                        <td>{{ $update['version'] }}</td>
                        <td>{{ $update['description'] }}</td>
                        <td>{{ $update['created_at'] }}</td>
                        <td>{{ $update['updated_at'] }}</td>
                        <td>{{ $update['writer'] }}</td>
                        <td style="text-align:center;">
                            <a href="updates/{{ $update['id'] }}/edit" class="edit" title="Edit"
                                data-toggle="tooltip"><i class="material-icons">&#xE254;</i></a>
                            @if ($update['is_visible'] == 1)
                                <a class="visibility" title="Visible" data-toggle="tooltip"><i
                                        class="material-icons">&#xe8f4;</i></a>
                            @else
                                <a class="visibility" title="Invisible" data-toggle="tooltip"><i
                                        class="material-icons">&#xe8f5;</i></a>
                            @endif
                            {{ html()->form('DELETE', url('admin/updates', $update['id']))->open() }}
                            <button class="delete" title="Delete" data-toggle="tooltip"><i
                                    class="material-icons">&#xE872;</i></button>
                            {{ html()->form()->close() }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}

    {{-- Hand written ones --}}

    {{-- Imports from the project --}}
@endsection
