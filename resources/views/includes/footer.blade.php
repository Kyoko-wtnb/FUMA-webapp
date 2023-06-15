<footer>
	<br/>
	<div class="row" style="color: #818588;">
		<div class="col-md-6 col-xs-6 col-sm-6" style="padding-left: 50px;">
			<p>
				Developed by: Kyoko Watanabe<br/>
				Update/maintenance: Douglas Wightman (d.p.wightman@vu.nl)<br/>
				Dept. Complex Trait Genetics at VU University Amsterdam
			</p>
		</div>
		<div class="col-md-6 col-xs-6 col-sm-6" style="text-align: right; padding-right: 50px;">
			<img class="footerimg" src="{{URL::asset('/image/ctg.svg')}}" height="70" width="180">
		</div>
	</div>
</footer>
<script type="text/javascipt">
	$(".alert button.close").click(function (e) {
		$(this).parent().fadeOut('slow');
	});
</script>
<script type="text/javascript" src="{!! URL::asset('js/sweetalert.min.js') !!}"></script>
<link rel="stylesheet" href="{!! URL::asset('css/sweetalert.css') !!}">
<script type="text/javascript" src="{!! URL::asset('js/HoldOn.min.js') !!}"></script>
<link rel="stylesheet" href="{!! URL::asset('css/HoldOn.min.css') !!}">
<script type="text/javascript" src="{!! URL::asset('js/fuma.js') !!}?131"></script>
<link rel="icon" href="{!! URL::asset('image/FUMAicon.png') !!}">
