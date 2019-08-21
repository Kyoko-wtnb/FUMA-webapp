<h3 id="workflow">3-step workflow with multiple scRNA-seq data sets</h3>
Since integration of scRNA-seq across datasets is highly challenging due to
complex batch effects, the 3-step workflow is aimed to bypass this problem by
systematically compare cell type associations across datasets using conditional analyses.
<br/><br/>

<div style="padding-left: 40px;">
	<div class="row">
		<div class="col-md-6 col-sm-6 col-xs-6">
			<h4><strong>Step 1. per dataset cell type analysis</strong></h4>
			<p>
				In the first step, MAGMA cell specificity analyses are performed for
				each of the user selected datasets separately using the regression model
				described in the previous section.<br/>
				Multiple testing correction is applied to the results for all tested
				cell type across datasets and significant cell types are retained for the
				next step.
				For example, when dataset A with 5 cell types and B with 10 cell types
				are selected, then multiple test correction is performed for 15 tested cell types.
				<br/>
				Note that outputs (both plots in result page and output files) also include
				adjusted P-value per dataset.
			</p>
			<h4><strong>Step 2. within dataset conditional analysis</strong></h4>
			<p>
				The second step is a within dataset conditional analysis.
				It is often the case that there are multiple similar cell types defined
				in a scRNA-seq dataset, especially when the resolution of cell types is
				high. The gene expression profiles of those cell types tend to strongly
				correlate with each other, and when a cell type is strongly associated
				with a trait it is therefore not clear whether that reflects a genuine
				involvement of that cell type or whether there is confounding due to
				expression in another cell type correlated with it. <br/>
				In step 2, a systematical step-wise conditional analysis per dataset
				is performed, by setting thresholds for proportional significance (\(PS\))
				of the conditional P-value of a cell type relative to the marginal P-value as
				described in the table. <br/>
				\(PS\) is defined as<br/>

				$$PS_{a,b}=-log10(p_{a,b})/-log10(p_a)$$

				where \(p_a\) is the marginal P-value for the cell type \(a\) using the base-model

				$$Z = \beta_0 + E_a\beta_{E_a} + A\beta_A + B\beta_B + \epsilon$$

				and \(p_{a,b}\) is the conditional P-value of the cell type
				\(a\) conditioning on the cell type \(b\) using the following model,

				$$Z = \beta_0 + E_a\beta_{E_a} + E_b\beta_{E_b} + A\beta_A + B\beta_B + \epsilon$$

				In summary, forward selection (retain the cell type
				with the lowest marginal P-value) was performed for a pair of cell types
				which were jointly explained (\(PS_{a,b}\)&lt;0.2 and \(PS_{b,a}\)&lt;0.2) or
				one association was mainly driving the other’s (\(PS_{a,b}\)&ge;0.5 and \(p_{b,a}\)&lt;0.05,
				or \(PS_{a,b}\)&ge;0.8 and \(PS_{b,a}\)&lt;0.5).
				In the case of partially joint associations (\(PS_{a,b}\)&ge;0.5 and \(PS_{b,a}\)&ge;0.5) or
				independent (\(PS_{a,b}\)&ge;0.8 and \(PS_{b,a}\)&ge;0.8), both cell types were retained.
				<br/>

			</p>
		</div>
		<div class="col-md-6 col-sm-6 col-xs-6">
			<img src="{!! URL::asset('/image/cellWorkflow.png') !!}" style="width:80%"/>
		</div>
	</div>

	Forward selection criteria when cell type \(a\) showed lower marginal P-value than cell type \(b\)
	<br/>
	(scenarios are ordered by the priority)
	<table class="table table-bordered">
		<thead>
			<th>Scenario</th>
			<th>Cell type a</th>
			<th>Cell type b</th>
			<th style="width:10%">Cell type a state</th>
			<th style="width:10%">Cell type b state</th>
			<th>Description</th>
		</thead>
		<tr>
			<td>1</td>
			<td>\(PS_{a,b}&ge;0.8\)</td>
			<td>\(PS_{b,a}&ge;0.8\)</td>
			<td>indep</td>
			<td>indep</td>
			<td>
				The association of cell type \(a\) and \(b\) are independent.
			</td>
		</tr>
		<tr>
			<td>2</td>
			<td>\(p_{a,b}&ge;0.05\)</td>
			<td>\(p_{b,a}&ge;0.05\)</td>
			<td>join</td>
			<td>joint-drop</td>
			<td>
				The association of cell type \(a\) and \(b\) are depending each other,
				and the model cannot distinguish association of two cell types.
				In this case, cell type \(a\) is retained and \(b\) is dropped as
				cell type \(a\) has more significant marginal P-value,
				but it does not mean association of cell type \(a\) is true and \(b\) is not.
			</td>
		</tr>
		<tr>
			<td>3</td>
			<td>\(PS_{a,b}&lt;0.2\)</td>
			<td>\(PS_{b,a}&lt;0.2\)</td>
			<td>joint</td>
			<td>joint-drop</td>
			<td>
				Similar to the scenario 2, but the association of cell type \(a\) and \(b\) are
				not completely explained by each other.
				In this case, only cell type \(a\) is retained as the significance of
				cell type \(b\) drop to less than 20% of the marginal association.
				The output (state of cell types) is exactly the same as scenario 2,
				however there might be still some signals specific to each cell type \(a\) and \(b\).
			</td>
		</tr>
		<tr>
			<td>4</td>
			<td>\(PS_{a,b}&ge;0.5\)</td>
			<td>\(p_{b,a}&ge;0.05\)</td>
			<td>main</td>
			<td>drop</td>
			<td>
				The association of cell type \(b\) is completely depending on
				the association of cell type \(a\).
				Only cell type \(a\) is retained.
			</td>
		</tr>
		<tr>
			<td>5</td>
			<td>\(PS_{a,b}&ge;0.8\)</td>
			<td>\(PS_{b,a}&lt;0.2\)</td>
			<td>main</td>
			<td>partial-drop</td>
			<td>
				The association of cell type \(b\) is mostly depending on the association of
				cell type \(a\) but cell type \(a\) cannot completely explain the association of
				cell type \(b\).
				In this case, only cell type \(a\) is retained as the significance of cell type \(b\)
				drop to less than 20% of the marginal association,
				however there are some amount of signals remained (since P-value is still less than 0.05).
			</td>
		</tr>
		<tr>
			<td>6</td>
			<td>\(PS_{a,b}&ge;0.5\)</td>
			<td>\(PS_{b,a}&ge;0.5\)</td>
			<td>partial-joint</td>
			<td>partial-joint</td>
			<td>
				The association of cell type \(a\) and \(b\) are only partially
				explained by each other but majority of signals are coming from the
				independent associations. Both cell type \(a\) and \(b\) are retained.
			</td>
		</tr>
		<tr>
			<td>7</td>
			<td>\(PS_{a,b}&ge;0.2\)</td>
			<td>\(PS_{b,a}&ge;0.2\)</td>
			<td>partial-joint</td>
			<td>partial-joint-drop</td>
			<td>
				Similar to scenario 6 but larger proportion of signals are explained
				by each other. In this case, only cell type \(a\) is retained as cell type \(b\)
				remain less than 20% of marginal significance, however there might still be specific
				underlying signal for cell type \(b\).
			</td>
		</tr>
		<tr>
			<td>8</td>
			<td>\(PS_{a,b}&ge;0.2\)</td>
			<td>\(p_{b,a}&ge;0.05\)</td>
			<td>partial-joint</td>
			<td>joint-drop</td>
			<td>
				The association of cell type \(b\) is completely explained by cell type \(a\)
				but there are part of association of cell type \(a\) dependent on cell type \(b\).
				In this case, only cell type \(a\) is retained.
			</td>
		</tr>
		<tr>
			<td>9</td>
			<td>\(PS_{a,b}&ge;0.2\)</td>
			<td>\(PS_{b,a}&lt;0.2\)</td>
			<td>partial-joint</td>
			<td>partial-joint-drop</td>
			<td>
				The association of cell type \(b\) is mostly explained by cell type \(a\)
				but there are part of associations dependent on each other.
				In this case, only cell type \(a\) is retained.
			</td>
		</tr>
	</table>

	<p>
		Note that when associations of two cell types are jointly explained,
		only one cell type with the lowest marginal P-value is retained for the third step.
		However, this does not mean the discarded cell type is less important
		than the retained cell type, but the result suggests that the associations
		of these two cell types cannot be distinguished.
		<br/>
		Although conditional P-values are often proportional to marginal P-values,
		it is possible that cell type with higher marginal P-value results in
		less conditional P-value for a pair of cell types (i.e. \(p_{b,a}\)&lt;\(p_{a,b}\)).
		Therefore, when \(PS_{a,b}\)&lt;0.2 and \(PS_{b,a}\)&ge;0.2,
		the order of cell types was flipped for forward selection.
		<br/>
		Although only retained cell types were used for the third step, the
		results of within dataset conditional analyses for any pair of cell
		types were further breakdown into 8 categories as described in the table.
		This is to provide better understanding of the relationship of two
		significantly associated cell types.
		For example, in both scenario 4 and 5, cell type B is dropped and
		cell type A is considered as the main driver of the association.
		However, in scenario 4, association of cell type B
		cannot be completely explained by cell type A as conditional P-value of
		cell type B is still &lt;0.05. Therefore, there might still be a unique
		signal to cell type B, however, as large amount of significance is dropped,
		the cell type B is not retained for the further step.
		<br/>
		<span class="info"><i class="fa fa-info"></i>
			Note that step 2 is only performed for dataset where more than one cell types
			reached significance after multiple testing correction across datasets.
		</span>
	</p>

	<h4><strong>Step 3. cross datasets conditional analysis</strong></h4>
	<p>
		The last step is to unravel relationships between significantly associated
		cell types across datasets.
		Although the absolute gene expression values in different datasets are not
		directly comparable, cross-datasets conditional analysis allows us to test
		the extent to which the significant gene expression profiles found in
		different data sets reflect the same or similar association signals.
		The analysis is performed for all possible cross-dataset pairs of
		significant cell types retained from the second step.
		Then the \(PS\) of the cross-datasets (CD) conditional P-value of a cell type
		relative to the CD marginal P-value is computed for each cell type of all possible pairs.
		<br/>
		For each pair of cell types from different datasets, the following
		three regression models were tested to incorporate the effect of
		the average expression from the other dataset:
		<br/>

		$$Z=\beta_0 + E_c1\beta_{E_{c1}} + A_1\beta_{A_1} + A_2\beta_{A_2} + B\beta_B + \epsilon$$
		$$Z=\beta_0 + E_c2\beta_{E_{c2}} + A_1\beta_{A_1} + A_2\beta_{A_2} + B\beta_B + \epsilon$$
		$$Z=\beta_0 + E_c1\beta_{E_{c1}} + E_c2\beta_{E_{c2}} + A_1\beta_{A_1} + A_2\beta_{A_2} + B\beta_B + \epsilon$$

		where \(E_{cx}\) is an average log transformed expression of cell type \(c\) from
		dataset \(x\), and \(A_x\) is an average expression across cell types in dataset \(x\).
		In this step, we define P-value of testing alternative hypothesis \(\beta_{E_{cx}}&gt;\)0
		from 1st and 2nd models as CD marginal P-value,
		and \(\beta_{E_{cx}}\)&gt;0 from 3rd model as CD conditional P-value for a cell type \(c\)
		from a dataset \(x\).
		<br/>
		Note that, when associations of two cell types from
		different datasets with a trait are largely disappeared by conditioning
		on each other, it suggests that associations of those cell types were
		driven by similar genetic signals but this does not measure the similarity
		of two cell types (i.e. it cannot be concluded that the cell types from
		the different datasets are the same).
		<br/>
		<span class="info"><i class="fa fa-info"></i>
			Note that step 3 is only performed where there are significant cell types from
			more than one datasets.
		</span>
		<br/>
		<span class="info"><i class="fa fa-info"></i>
			Please be aware that, some of scRNA-seq, there are multiple datasets
			available from a single scRNA-seq data resource.
			For example, Tabula Muris FACS data, one dataset contains all cell types from all 20 tissues,
			and there are 20 datasets for each tissue separately.
			When both TabulaMuris_FACS_all and TabulaMuris_FACS_Aorta datasets are selected, for instance,
			exact same cell types in the Aorta dataset exist in the dataset with all tissues.
			Testing both datasets are still relevant as average expression across cell types is different for each dataset,
			however, step 3 is not relevant in this case as they are exactly the same cell type.
			When step 3 is activated, FUMA will still perform all possible pair of
			significant cell types across datasets.
			However the pair of exact same cell type will be collinear (MAGMA outputs NA for such pairs).
		</span>
	</p>
</div>
