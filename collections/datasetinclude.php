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
<div id="dataset-tools" class="dataset-div" style="clear:both;display:none">
	<fieldset>
		<legend><?php echo (isset($LANG['DATASET_MANAGEMENT'])?$LANG['DATASET_MANAGEMENT']:'Dataset Management'); ?></legend>
		<?php
		$datasetArr = $collManager->getDatasetArr();
		?>
		<div style="padding:5px;float:left;"><?php echo (isset($LANG['DATASET_TARGET'])?$LANG['DATASET_TARGET']:'Dataset target'); ?>: </div>
		<div style="padding:5px;float:left;">
			<select name="targetdatasetid" onchange="datasetSelectChanged(this)">
				<option value="">------------------------</option>
				<?php
				if($datasetArr){
					foreach($datasetArr as $datasetID => $datasetName){
						echo '<option value="'.$datasetID.'">'.$datasetName.'</option>';
					}
				}
				else echo '<option value="">'.(isset($LANG['NO_DATASETS'])?$LANG['NO_DATASETS']:'no existing datasets available').'</option>';
				?>
				<option value="">----------------------------------</option>
				<option value="--newDataset"><?php echo (isset($LANG['CREATE_DATASET'])?$LANG['CREATE_DATASET']:'Create New Dataset'); ?></option>
			</select>
		</div>
		<div style="clear:both;margin:5px 0px">
			<span class="checkbox-elem"><input name="selectall" type="checkbox" onclick="selectAllDataset(this)" /></span>
			<span style="padding:10px;"><?php echo (isset($LANG['SELECT_ALL_RECORDS'])?$LANG['SELECT_ALL_RECORDS']:'Select all records on page'); ?></span>
		</div>
		<div style="clear:both;">
			<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
			<div style="padding:5px 0px;float:left;"><button name="action" type="submit" value="addSelectedToDataset" onclick="return hasSelectedOccid(this.form)"><?php echo (isset($LANG['ADD_SELECTED'])?$LANG['ADD_SELECTED']:'Add Selected Records to Dataset'); ?></button></div>
			<div style="padding:5px;float:left;"><button name="action" type="submit" value="addAllToDataset"><?php echo (isset($LANG['ADD_COMPLETE_QUERY'])?$LANG['ADD_COMPLETE_QUERY']:'Add Complete Query to Dataset'); ?></button></div>
		</div>
	</fieldset>
</div>