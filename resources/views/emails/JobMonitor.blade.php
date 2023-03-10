<html>
<head><h3>FUMA Monitor Report for {{ $date }}</h3></head>
<body>
<p>
	Total user: {{ $totalUsers }}<br/>
	Total job submitted: {{ $totalNjobs }}<br/>
	Currently runnning jobs: {{ $running }}<br/>
	Currently queued jobs: {{ $queued }}<br/>
	Average job submission per day: {{ $dateavg }}<br/>
	<br/>
</p>
</body>
</html>
