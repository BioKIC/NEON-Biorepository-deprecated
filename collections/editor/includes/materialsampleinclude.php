<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorMaterialSample.php');
header("Content-Type: text/html; charset=".$CHARSET);

$occid = $_GET['occid'];

$materialSampleManager = new OccurrenceEditorMaterialSample();
$materialSampleManager->setOccId($occid);
$msArr = $materialSampleManager->getMaterialSampleArr();
$controlTermArr = $materialSampleManager->getMSTypeControlValues();
?>
<script>
</script>
<fieldset>
	<legend>Material Sample</legend>
	<div style="clear:both">
		See <a href="https://tools.gbif.org/dwca-validator/extension.do?id=http://data.ggbn.org/schemas/ggbn/terms/MaterialSample" target="_blank">GGBN Material Sample Extension</a> documentation
	</div>
	<div style="clear:both">
		<div id="smTypeDiv">
			<label><?php echo (defined('MS_TYPE_LABEL')?MS_TYPE_LABEL:'Material Sample Type'); ?></label>
			<?php
			if(isset($controlTermArr['materialsampletype'])){
				?>
				<select name="ms_materialsampletype">
					<option value="">-------</option>
					<?php
					foreach($controlTermArr['materialsampletype'] as $t){
						echo '<option value="" '.($msArr && $msArr['materialsampletype'] == $t?'selected':'').'>'.$t.'</option>';
					}
					?>
				</select>
				<?php
			}
			else{
				?>
				<input type="text" name="ms_materialsampletype" value="<?php echo isset($msArr['materialsampletype'])?$msArr['materialsampletype']:''; ?>" />
				<?php
			}
			?>
		</div>
		<div id="smGuidDiv">
			<label><?php echo (defined('MS_GUID_LABEL')?MS_GUID_LABEL:'Global Unique ID (GUID)'); ?></label>
			<input type="text" name="ms_guid" value="<?php echo isset($msArr['guid'])?$msArr['guid']:''; ?>" />
		</div>
		<div id="smConcentrationDiv">
			<input type="text" name="ms_concentration" value="<?php echo isset($msArr['concentration'])?$msArr['concentration']:''; ?>" />
			<?php
			if(isset($controlTermArr['concentrationUnit'])){
				?>
				<select name="ms_concentrationUnit">
					<option value="">-------</option>
					<?php
					foreach($controlTermArr['concentrationUnit'] as $t){
						echo '<option value="" '.($msArr && $msArr['concentrationUnit'] == $t?'selected':'').'>'.$t.'</option>';
					}
					?>
				</select>
				<?php
			}
			else{
				?>
				<input type="text" class="unit-field" name="ms_concentrationUnit" value="<?php echo isset($msArr['concentrationUnit'])?$msArr['concentrationUnit']:''; ?>" />
				<?php
			}
			?>
		</div>
		<div id="smConcentrationMethodDiv">
			<?php
			if(isset($controlTermArr['concentrationUnit'])){
				?>
				<select name="ms_concentrationMethod">
					<option value="">-------</option>
					<?php
					foreach($controlTermArr['concentrationMethod'] as $t){
						echo '<option value="" '.($msArr && $msArr['concentrationMethod'] == $t?'selected':'').'>'.$t.'</option>';
					}
					?>
				</select>
				<?php
			}
			else{
				?>
				<input type="text" class="unit-field" name="ms_concentrationMethod" value="<?php echo isset($msArr['concentrationMethod'])?$msArr['concentrationMethod']:''; ?>" />
				<?php
			}
			?>
		</div>
		<div id="smRatioOfAbsorbance260_230Div">
			<label><?php echo (defined('MS_RATIOOFABSORBANCE260_230_LABEL')?MS_RATIOOFABSORBANCE260_230_LABEL:'Ratio of Absorbance (260/230)'); ?></label>
			<input type="text" name="ms_ratioOfAbsorbance260_230" value="<?php echo isset($msArr['ratioOfAbsorbance260_230'])?$msArr['ratioOfAbsorbance260_230']:''; ?>" />
		</div>
		<div id="smRatioOfAbsorbance260_280Div">
			<label><?php echo (defined('MS_RATIOOFABSORBANCE260_280_LABEL')?MS_RATIOOFABSORBANCE260_280_LABEL:'Ratio of Absorbance (260/280)'); ?></label>
			<input type="text" name="ms_ratioOfAbsorbance260_280" value="<?php echo isset($msArr['ratioOfAbsorbance260_280'])?$msArr['ratioOfAbsorbance260_280']:''; ?>" />
		</div>
		<div id="smVolumeDiv">
			<input type="text" name="ms_volume" value="<?php echo isset($msArr['volume'])?$msArr['volume']:''; ?>" />
			<?php
			if(isset($controlTermArr['volumeUnit'])){
				?>
				<select name="ms_volumeUnit">
					<option value="">-------</option>
					<?php
					foreach($controlTermArr['volumeUnit'] as $t){
						echo '<option value="" '.($msArr && $msArr['volumeUnit'] == $t?'selected':'').'>'.$t.'</option>';
					}
					?>
				</select>
				<?php
			}
			else{
				?>
				<input type="text" class="unit-field" name="ms_volumeUnit" value="<?php echo isset($msArr['volumeUnit'])?$msArr['volumeUnit']:''; ?>" />
				<?php
			}
			?>
		</div>
		<div id="smWeightDiv">
			<input type="text" name="ms_weight" value="<?php echo isset($msArr['weight'])?$msArr['weight']:''; ?>" />
			<?php
			if(isset($controlTermArr['weightUnit'])){
				?>
				<select name="ms_weightUnit">
					<option value="">-------</option>
					<?php
					foreach($controlTermArr['weightUnit'] as $t){
						echo '<option value="" '.($msArr && $msArr['weightUnit'] == $t?'selected':'').'>'.$t.'</option>';
					}
					?>
				</select>
				<?php
			}
			else{
				?>
				<input type="text" class="unit-field" name="ms_weightUnit" value="<?php echo isset($msArr['weightUnit'])?$msArr['weightUnit']:''; ?>" />
				<?php
			}
			?>
		</div>
		<div id="smGuidDiv">
			<label><?php echo (defined('MS_GUID_LABEL')?MS_GUID_LABEL:'Global Unique ID (GUID)'); ?></label>
			<input type="text" name="ms_guid" value="<?php echo isset($msArr['guid'])?$msArr['guid']:''; ?>" />
		</div>
		<div id="smGuidDiv">
			<label><?php echo (defined('MS_GUID_LABEL')?MS_GUID_LABEL:'Global Unique ID (GUID)'); ?></label>
			<input type="text" name="ms_guid" value="<?php echo isset($msArr['guid'])?$msArr['guid']:''; ?>" />
		</div>
	</div>

weightMethod, purificationMethod, quality, qualityRemarks, qualityCheckDate, sampleSize, sieving, dnaHybridization, dnaMeltingPoint,
estimatedSize, poolDnaExtracts, sampleDesignation, initialTimestamp
</fieldset>