@extends('layouts.master')

@section('content')
    <div class="container" style="padding-top:50px;">
        <div style="text-align: center;">
            <h2>Analysis Dashboard</h2>
            <h2>This is a test analysis page</h2>

            <table class="table table-bordered table-sm table-striped">
                <tr>
                    @foreach ($data->first() as $key => $value)
                        <th>{{ $key }}</th>
                    @endforeach
                </tr>
                @foreach ($data as $items)
                    <tr>
                        @foreach ($items as $key => $value)
                            @if ($key != 'tools')
                                <td>{{ $value }}</td>
                            @elseif ($key == 'tools' && !empty($value))
                                <td>
                                    <table class="table table-sm table-borderless table-striped">
                                        <tr>
                                            @foreach (array_keys($value[0]) as $sub_keys => $sub_key)
                                                <th>{{ $sub_key }}</th>
                                            @endforeach
                                        </tr>
                                        @foreach ($value as $sub_items)
                                            <tr>
                                                @foreach ($sub_items as $sub_sub_key => $sub_sub_value)
                                                    @if ($sub_sub_key == 'tool_params' && !empty($sub_sub_value))
                                                        @foreach ($sub_sub_value as $param)
                                                            @foreach ($param as $param_key => $param_value)
                                                                @if ($param_key == 'param_name')
                                                                    <td> {{ $param_value }} </td>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        {{-- @php print_r($sub_sub_value); @endphp --}}
                                                    @else
                                                        <td>{{ $sub_sub_value }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                        @endforeach



                                    </table>
                                </td>
                            @endif
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
