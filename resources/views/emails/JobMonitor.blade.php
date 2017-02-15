<html>
<head><h3>FUMA Monitor Report for {{ $date }}</h3></head>
<body>
<p>
  Total job submitted: {{ $totalNjobs }}<br/>
  Currently runnning jobs: {{ $running }}<br/>
  Currently queued jobsL {{ $queued }}<br/>
  Average job submission per day {{ $dateavg }}<br/>
  <br/>
</p>
<p><h4>Running jobs</h4>
  {{ $runTable }}
</p>

<p><h4>Queued jobs</h4>
  {{ $queTable }}
</p>

</body>
</html>
