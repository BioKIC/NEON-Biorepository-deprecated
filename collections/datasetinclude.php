<?php
$datasetArr = $collManager->getDatasetArr();
?>
<script>
var isSubmitSelectedAction = false;

function validateOccurListForm(f){
	if(f.datasetid.value == "" && f.newtitle.value == ""){
		alert("Please select an existing dataset to append to or enter a title to create a new dataset");
		return false;
	}
	else if(isSubmitSelectedAction){
		var formVerified = false;
		for(var h=0;h<f.length;h++){
			if(f.elements[h].name == "occid[]" && f.elements[h].checked){
				formVerified = true;
				break;
			}
		}
		if(!formVerified){
			alert("Please select at least one occurrence to be added to the dataset");
			return false;
		}
	}
	return true;
}

function selectAllDataset(cbElem){
	var boxesChecked = true;
	if(!cbElem.checked) boxesChecked = false;
	var f = cbElem.form;
	for(var i=0;i<f.length;i++){
		if(f.elements[i].name == "occid[]") f.elements[i].checked = boxesChecked;
	}
}

function datasetSelectChanged(selElem){
	if(selElem.value == 'refreshList'){

	}
}
</script>
<div class="datasetDiv" style="clear:both;display:none">
	<fieldset style="margin:10px">
		<legend><b>Dataset Management</b></legend>
		<div style="padding:5px;float:left;">Dataset target: </div>
		<?php
		if($datasetArr){
			?>
			<div style="padding:5px;float:left;">
				<select name="datasetid" onchange="datasetSelectChanged(this)">
					<option value="">Select an Existing Dataset</option>
					<option value="">------------------------</option>
					<?php
					foreach($datasetArr as $datasetID => $datasetName){
						echo '<option value="'.$datasetID.'">'.$datasetName.'</option>';
					}
					?>
					<option value="refreshList">Refresh List</option>
				</select>
				-- OR --
			</div>
			<?php
		}
		?>
		<div style="padding:5px;float:left;">
			Add to a New Dataset: <input name="name" type="text" value="" />
		</div>
		<div style="clear:both;">
			<div style="height:20px;width:20px;margin:5px;padding:5px;border:1px dashed orange;float:left;">
				<input name="selectall" type="checkbox" onclick="selectAllDataset(this)" />
			</div>
			<div style="padding:10px;">Select All Records</div>
		</div>
		<div style="clear:both;">
			<div style="padding:5px 0px;float:left;"><button name="submitaction" type="submit" value="addSelectedToDataset">Add Selected Records to Dataset</button></div>
			<div style="padding:5px;float:left;"><button name="submitaction" type="submit" value="addAllToDataset">Add Complete Query to Dataset</button></div>
		</div>
	</fieldset>
</div>