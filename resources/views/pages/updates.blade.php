@extends('layouts.master')

@section('content')
    <div class="container" style="padding-top: 50px;">
        <table class="table table-bordered" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th style="width: 15%;">Date</th>
                    <th style="width: 15%;">Title</th>
                    <th style="width: 15%;">Version</th>
                    <th style="width: 55%;">Description</th>
                </tr>
            </thead>
            <tbody>
                @isset($updates)
                    @foreach ($updates as $update)
                        <tr style="word-wrap: break-word">
                            <td>{{ $update['created_at'] }}</td>
                            <td>{{ $update['title'] }}</td>
                            <td>{{ $update['version'] }}</td>
                            <td>{!! $update['description'] !!}</td>
                        </tr>
                    @endforeach
                @endisset
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}

    {{-- Imports from the project --}}

    {{-- Hand written ones --}}
    <script type="text/javascript">
        var loggedin = "{{ Auth::check() }}";
    </script>
@endsection
