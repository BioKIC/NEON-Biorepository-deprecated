<script>
	function selectAllDataset(cbElem){
		var boxesChecked = true;
		if(!cbElem.checked) boxesChecked = false;
		var f = cbElem.form;
		for(var i=0;i<f.length;i++){
			if(f.elements[i].name == "occid[]") f.elements[i].checked = boxesChecked;
		}
	}
</script>
<div class="datasetDiv" style="clear:both;display:none">
	<fieldset style="margin:10px">
		<legend><b>Dataset Management</b></legend>
		<div style="padding:5px;float:left;">Dataset target: </div>
		<?php
		$datasetArr = $collManager->getDatasetArr();
		if($datasetArr){
			?>
			<div style="padding:5px;float:left;">
				<select name="targetdatasetid" onchange="datasetSelectChanged(this)">
					<option value="">Select an Existing Dataset</option>
					<option value="">------------------------</option>
					<?php
					foreach($datasetArr as $datasetID => $datasetName){
						echo '<option value="'.$datasetID.'">'.$datasetName.'</option>';
					}
					?>
					<option value="--newDataset">Create New Dataset</option>
				</select>
			</div>
			<?php
		}
		?>
		<div style="clear:both;">
			<div style="height:20px;width:20px;margin:5px;padding:5px;border:1px dashed orange;float:left;">
				<input name="selectall" type="checkbox" onclick="selectAllDataset(this)" />
			</div>
			<div style="padding:10px;">Select all records on page</div>
		</div>
		<div style="clear:both;">
			<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
			<div style="padding:5px 0px;float:left;"><button name="action" type="submit" value="addSelectedToDataset" onclick="return hasSelectedOccid(this.form)">Add Selected Records to Dataset</button></div>
			<div style="padding:5px;float:left;"><button name="action" type="submit" value="addAllToDataset">Add Complete Query to Dataset</button></div>
		</div>
	</fieldset>
</div>