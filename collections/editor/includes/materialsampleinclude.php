<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorMaterialSample.php');
include_once($SERVER_ROOT.'/collections/editor/includes/config/materialSampleVars.php');
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
			<label><?php echo $MS_TYPE_LABEL; ?></label>
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
			<label><?php echo $MS_GUID_LABEL; ?></label>
			<input type="text" name="ms_guid" value="<?php echo isset($msArr['guid'])?$msArr['guid']:''; ?>" />
		</div>
		<div id="smConcentrationDiv">
			<label><?php echo $MS_CONCENTRATION_LABEL; ?></label>
			<input type="text" name="ms_concentration" value="<?php echo isset($msArr['concentration'])?$msArr['concentration']:''; ?>" />
		</div>
		<div id="smConcentrationUnitDiv">
			<label><?php echo $MS_CONCENTRATION_UNIT_LABEL; ?></label>
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
			<label><?php echo $MS_CONCENTRATION_METHOD_LABEL; ?></label>
			<?php
			if(isset($controlTermArr['concentrationMethod'])){
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
			<label><?php echo $MS_RATIOOFABSORBANCE260_230_LABEL; ?></label>
			<input type="text" name="ms_ratioOfAbsorbance260_230" value="<?php echo isset($msArr['ratioOfAbsorbance260_230'])?$msArr['ratioOfAbsorbance260_230']:''; ?>" />
		</div>
		<div id="smRatioOfAbsorbance260_280Div">
			<label><?php echo $MS_RATIOOFABSORBANCE260_280_LABEL; ?></label>
			<input type="text" name="ms_ratioOfAbsorbance260_280" value="<?php echo isset($msArr['ratioOfAbsorbance260_280'])?$msArr['ratioOfAbsorbance260_280']:''; ?>" />
		</div>
		<div id="smVolumeDiv">
			<label><?php echo $MS_VOLUME_LABEL; ?></label>
			<input type="text" name="ms_volume" value="<?php echo isset($msArr['volume'])?$msArr['volume']:''; ?>" />
		</div>
		<div id="smVolumeUnitDiv">
			<label><?php echo $MS_VOLUME_UNIT_LABEL; ?></label>
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
			<label><?php echo $MS_WEIGHT_LABEL; ?></label>
			<input type="text" name="ms_weight" value="<?php echo isset($msArr['weight'])?$msArr['weight']:''; ?>" />
		</div>
		<div id="smWeightUnitDiv">
			<label><?php echo $MS_WEIGHT_UNIT_LABEL; ?></label>
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
		<div id="smWeightMethodDiv">
			<label><?php echo $MS_WEIGHT_METHOD_LABEL; ?></label>
			<?php
			if(isset($controlTermArr['weightMethod'])){
				?>
				<select name="ms_weightMethod">
					<option value="">-------</option>
					<?php
					foreach($controlTermArr['weightMethod'] as $t){
						echo '<option value="" '.($msArr && $msArr['weightMethod'] == $t?'selected':'').'>'.$t.'</option>';
					}
					?>
				</select>
				<?php
			}
			else{
				?>
				<input type="text" class="unit-field" name="ms_weightMethod" value="<?php echo isset($msArr['weightMethod'])?$msArr['weightMethod']:''; ?>" />
				<?php
			}
			?>
		</div>
		<div id="smPurificationMethodDiv">
			<label><?php echo (defined('MS_PURIFICATION_METHOD_LABEL')?MS_PURIFICATION_METHOD_LABEL:'Purification Method'); ?></label>
			<?php
			if(isset($controlTermArr['purificationMethod'])){
				?>
				<select name="ms_purificationMethod">
					<option value="">-------</option>
					<?php
					foreach($controlTermArr['purificationMethod'] as $t){
						echo '<option value="" '.($msArr && $msArr['purificationMethod'] == $t?'selected':'').'>'.$t.'</option>';
					}
					?>
				</select>
				<?php
			}
			else{
				?>
				<input type="text" class="unit-field" name="ms_purificationMethod" value="<?php echo isset($msArr['purificationMethod'])?$msArr['purificationMethod']:''; ?>" />
				<?php
			}
			?>
		</div>
		<div id="smQualityDiv">
			<label><?php echo $MS_QUALITY_LABEL; ?></label>
			<?php
			if(isset($controlTermArr['quality'])){
				?>
				<select name="ms_quality">
					<option value="">-------</option>
					<?php
					foreach($controlTermArr['quality'] as $t){
						echo '<option value="" '.($msArr && $msArr['quality'] == $t?'selected':'').'>'.$t.'</option>';
					}
					?>
				</select>
				<?php
			}
			else{
				?>
				<input type="text" name="ms_quality" value="<?php echo isset($msArr['quality'])?$msArr['quality']:''; ?>" />
				<?php
			}
			?>
		</div>
		<div id="smQualityRemarksDiv">
			<label><?php echo $MS_QUALITY_REMARKS_LABEL; ?></label>
			<input type="text" class="remarks-field" name="qualityRemarks" value="<?php echo isset($msArr['qualityRemarks'])?$msArr['qualityRemarks']:''; ?>" />
		</div>
		<div id="smQualityCheckDateDiv">
			<label><?php echo $MS_QUALITY_CHECK_DATE_LABEL; ?></label>
			<input type="date" name="qualityCheckDate" value="<?php echo isset($msArr['qualityCheckDate'])?$msArr['qualityCheckDate']:''; ?>" />
		</div>
		<div id="smSampleSizeDiv">
			<label><?php echo $MS_SAMPLE_SIZE_LABEL; ?></label>
			<input type="text" name="ms_sampleSize" value="<?php echo isset($msArr['sampleSize'])?$msArr['sampleSize']:''; ?>" />
		</div>
		<div id="smSievingDiv">
			<label><?php echo $MS_SIEVING_LABEL; ?></label>
			<input type="text" name="ms_sieving" value="<?php echo isset($msArr['sieving'])?$msArr['sieving']:''; ?>" />
		</div>
		<div id="smDnaHybridizationDiv">
			<label><?php echo $MS_DNA_HYBRIDIZATION_LABEL; ?></label>
			<input type="text" name="ms_dnaHybridization" value="<?php echo isset($msArr['dnaHybridization'])?$msArr['dnaHybridization']:''; ?>" />
		</div>
		<div id="smDnaMeltingPointDiv">
			<label><?php echo $MS_DNA_MELTING_POINT_LABEL; ?></label>
			<input type="text" name="ms_dnaMeltingPoint" value="<?php echo isset($msArr['dnaMeltingPoint'])?$msArr['dnaMeltingPoint']:''; ?>" />
		</div>
		<div id="smEstimatedSizeDiv">
			<label><?php echo $MS_ESTIMATED_SIZE_LABEL; ?></label>
			<input type="text" name="ms_estimatedSize" value="<?php echo isset($msArr['estimatedSize'])?$msArr['estimatedSize']:''; ?>" />
		</div>
		<div id="smPoolDnaExtractsDiv">
			<label><?php echo $MS_POOL_DNA_EXTRACTS_LABEL; ?></label>
			<input type="text" name="ms_poolDnaExtracts" value="<?php echo isset($msArr['poolDnaExtracts'])?$msArr['poolDnaExtracts']:''; ?>" />
		</div>
		<div id="smSampleDesignationDiv">
			<label><?php echo $MS_SAMPLE_DESIGNATION_LABEL; ?></label>
			<input type="text" name="ms_sampleDesignation" value="<?php echo isset($msArr['sampleDesignation'])?$msArr['sampleDesignation']:''; ?>" />
		</div>
	</div>
</fieldset>