{{-- Variables definition (they can be used inside project's js files or hand written js scripts) --}}
<script>
    var subdir = "{{ Config::get('app.subdir') }}";
    var loggedin = "{{ Auth::check() }}";
</script>

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> --}}
{{-- uptated to: --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
{{-- updated to: --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script> --}}

<script type="text/javascript" src="{!! URL::asset('js/fuma.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/HoldOn.min.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/sweetalert.min.js') !!}"></script>
<script type="text/javascript" src="{!! URL::asset('js/alerts.js')!!}"></script>