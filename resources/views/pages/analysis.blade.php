@extends('layouts.master')

@section('content')
    <div class="container" style="padding-top:50px;">
        <div style="text-align: center;">
            <h2>Analysis Dashboard</h2>
            <h2>This is a test analysis page</h2>

            <table class="table">
                <tr>
                    @foreach ($tools->first() as $key => $value)
                        <th>{{ $key }}</th>
                    @endforeach
                </tr>
                @foreach ($tools as $items)
                    <tr>
                        @foreach ($items as $key => $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </table>

        </div>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}

    {{-- Imports from the project --}}

    {{-- Hand written ones --}}
@endsection
