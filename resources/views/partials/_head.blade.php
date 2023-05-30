<meta charset="utf-8"/>
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<title>Functional Mapping and Annotation of Genome-wide association studies</title>

<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
{{-- can be updated to: --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous"> --}}

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.0/css/bootstrap-select.min.css"/>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>

<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/themes/smoothness/jquery-ui.css">

<link rel="stylesheet" href="{!! URL::asset('css/style.css') !!}"/>
<link rel="stylesheet" href="{!! URL::asset('css/sidebar.css') !!}"/>
<link rel="stylesheet" href="{!! URL::asset('css/sweetalert.css') !!}"/>
<link rel="stylesheet" href="{!! URL::asset('css/HoldOn.min.css') !!}"/>
{{-- <link rel="icon" href="{!! URL::asset('image/FUMAicon.png') !!}"/> --}}

@yield('stylesheets')