<html>
<head><h3>FUMA job failed</h3></head>
<body>

<p>
	Your submitted job (job ID: {{ $jobID }}, job title: {{ $jobtitle }}) has failed
	due to a timeout. The job timeout for your user is set to  {{ $timeout }} seconds,
	the job ran for {{ $elapsed }} seconds.
	<br/>
	For security reason, your input files are deleted already.
	<br/>
</p>
<p>
	You can post questions, suggestions and bug reports on Google Forum:
	<a href="https://groups.google.com/forum/#!forum/fuma-gwas-users">FUMA GWAS users</a><br/><br/>
	FUMA development team<br/>
	VU University Amsterdam<br/>
	Dept. Complex Trait Genetics<br/>
</p>
</body>
</html>
