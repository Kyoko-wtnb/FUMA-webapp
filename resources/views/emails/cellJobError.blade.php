<html>
	<head><h3>FUMA an error occured</h3></head>
	<body>
		<p>
			An error occured during the process of your cell type specificity analyses
			(job ID: {{ $jobID }}, job title: {{ $jobtitle }}) has been completed.<br/>
			Please make sure that your provided inputs meet all the requirments and check the followings.<br/>
			<ol>
				<li>Does your selected SNP2GENE job have MAGMA output?
					If you can see manhattan plot for gene-based test, this should not be the problem.
				</li>
				<li>Is your uoloaded file an output of MAGMA gene analysis with an extention "genes.raw"?</li>
				<li>Does your file contains Ensembl gene ID?
					Otherwise, don't forget to UNCHECK the option to indicate that you are using Ensembl gene ID.
				</li>
			</ol>
			If any of those doesn't solve the problem, please contact to the developper.
		</p>

		<p>
			Please do not hesitate to contact me if you have questions/suggestions regarding FUMA.<br/>
			You can also post questions, suggestions and bug reports on Google Forum: <a href="https://groups.google.com/forum/#!forum/fuma-gwas-users">FUMA GWAS users</a><br/><br/>
			Kyoko Watanabe<br/>
			VU University Amsterdam<br/>
			Dept. Complex Trait Genetics<br/>
			De Boelelaan 1085 WN-B628 1018HV Amsterdam The Netherlands<br/>
			k.watanabe@vu.nl<br/>
		</p>
	</body>
</html>
