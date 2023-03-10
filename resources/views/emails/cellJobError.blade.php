<html>
	<head><h3>FUMA an error occured</h3></head>
	<body>
		<p>
			An error occured during the process of your cell type specificity analyses
			(job ID: {{ $jobID }}, job title: {{ $jobtitle }}) has been completed.<br/>
			Please make sure that your provided inputs meet all the requirements and check the followings.<br/>
			<ol>
				<li>Does your selected SNP2GENE job have MAGMA output?
					If you can see manhattan plot for gene-based test, this should not be the problem.
				</li>
				<li>Is your uoloaded file an output of MAGMA gene analysis with an extension "genes.raw"?</li>
				<li>Does your file contains Ensembl gene ID?
					Otherwise, don't forget to UNCHECK the option to indicate that you are using Ensembl gene ID.
				</li>
			</ol>
			If any of those doesn't solve the problem, please contact to the developer.
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
