<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorMaterialSample.php');
header("Content-Type: text/html; charset=".$CHARSET);

$occid = $_GET['occid'];

$materialSampleManager = new OccurrenceEditorMaterialSample();
$materialSampleManager->setOccId($occid);
$materialSampleManager->getMaterialSampleArr();
?>
<script>
</script>
<fieldset>
	<legend>Material Sample</legend>
	<div style="clear:both">
		<div id="materialSampleType">
			<label><?php echo (defined('MATERIALSAMPLETYPELABEL')?MATERIALSAMPLETYPELABEL:'Material Sample Type'); ?>
			<a href="#" onclick="return dwcDoc('materialSampleType')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a></label><br/>
			<select name="materialsampletype">
				<option value=""></option>
				<option value="" <?php echo (?'':''); ?>></option>
			</select>
			<input type="text" name="materialsampletype" value="<?php echo isset($occArr['materialsampletype'])?$occArr['materialsampletype']:''; ?>" onchange="fieldChanged('materialsampletype');" />
		</div>
		<div id="storageAgeDiv">
			<?php echo (defined('STORAGEAGELABEL')?STORAGEAGELABEL:'Storage Age'); ?>
			<a href="#" onclick="return dwcDoc('storageAge')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="storageage" value="<?php echo isset($occArr['storageage'])?$occArr['storageage']:''; ?>" onchange="fieldChanged('storageage');" />
		</div>
		<div id="localStageDiv">
			<?php echo (defined('LOCALSTAGELABEL')?LOCALSTAGELABEL:'Local Stage'); ?>
			<a href="#" onclick="return dwcDoc('localStage')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="localstage" value="<?php echo isset($occArr['localstage'])?$occArr['localstage']:''; ?>" onchange="fieldChanged('localstage');" />
		</div>
	</div>

	SELECT msID, materialSampleType, concentration, concentrationUnit, concentrationMethod, ratioOfAbsorbance260_230, ratioOfAbsorbance260_280, volume, volumeUnit,
weight, weightUnit, weightMethod, purificationMethod, quality, qualityRemarks, qualityCheckDate, sampleSize, sieving, dnaHybridization, dnaMeltingPoint,
estimatedSize, poolDnaExtracts, sampleDesignation, initialTimestamp
</fieldset>