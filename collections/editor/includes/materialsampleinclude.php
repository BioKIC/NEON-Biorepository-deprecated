<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorMaterialSample.php');
include_once($SERVER_ROOT.'/collections/editor/includes/config/materialSampleVars.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=collections/misc/collprofiles.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$occid = $_REQUEST['occid'];
$matSampleID = isset($_REQUEST['matSampleID'])?$_REQUEST['matSampleID']:'';
$collid = isset($_REQUEST['collid'])?$_REQUEST['collid']:'';
$formSubmit = isset($_REQUEST['formsubmit'])?$_REQUEST['formsubmit']:'';

$materialSampleManager = new OccurrenceEditorMaterialSample();

//Sanitation
if(!is_numeric($occid)) $occid = 0;
if(!is_numeric($matSampleID)) $matSampleID = 0;
if(!is_numeric($collid)) $collid = 0;
$materialSampleManager->cleanFormData($_POST);

$materialSampleManager->setOccId($occid);
$materialSampleManager->setMatSampleID($matSampleID);

$isEditor = false;
if($isEditor){
	if($formSubmit){

	}
}

$msArr = $materialSampleManager->getMaterialSampleArr();
$controlTermArr = $materialSampleManager->getMSTypeControlValues();
?>
<script>
	$(document).ready(function() {
		$("#batchtaxagroup").autocomplete({
			source: function( request, response ) {
				$.getJSON( "rpc/taxalist.php", { term: request.term, t: "batch" }, response );
			},
			minLength: 3,
			autoFocus: true,
			select: function( event, ui ) {
				if(ui.item) document.getElementById('batchtid').value = ui.item.id;
			}
		});

	});

</script>
<fieldset>
	<legend>Material Sample</legend>
	<div style="clear:both">
		See <a href="https://tools.gbif.org/dwca-validator/extension.do?id=http://data.ggbn.org/schemas/ggbn/terms/MaterialSample" target="_blank">GGBN Material Sample Extension</a> documentation
	</div>
	<form >
		<div style="clear:both">
			<div id="smSampleTypeDiv">
				<label><?php echo $MS_TYPE_LABEL; ?></label>
				<?php
				if(isset($controlTermArr['ommaterialsample']['sampleType'])){
					$limitToList = $controlTermArr['ommaterialsample']['sampleType']['l'];
					?>
					<select name="ms_sampleType" required>
						<option value="">-------</option>
						<?php
						foreach($controlTermArr['ommaterialsample']['sampleType']['v'] as $t){
							echo '<option '.($msArr && $msArr['sampleType'] == $t?'selected':'').'>'.$t.'</option>';
						}
						?>
					</select>
					<?php
				}
				else{
					?>
					<input type="text" name="ms_sampleType" value="<?php echo isset($msArr['materialsampletype'])?$msArr['materialsampletype']:''; ?>" required />
					<?php
				}
				?>
			</div>
			<div id="smCatalogNumberDiv">
				<label><?php echo $MS_CATALOG_NUMBER_LABEL; ?></label>
				<input type="text" name="ms_catalogNumber" value="<?php echo isset($msArr['catalogNumber'])?$msArr['catalogNumber']:''; ?>" />
			</div>
			<div id="smGuidDiv">
				<label><?php echo $MS_GUID_LABEL; ?></label>
				<input type="text" name="ms_guid" value="<?php echo isset($msArr['guid'])?$msArr['guid']:''; ?>" />
			</div>
			<div id="smConditionDiv">
				<label><?php echo $MS_CONDITION_LABEL; ?></label>
				<?php
				if(isset($controlTermArr['ommaterialsample']['condition'])){
					$limitToList = $controlTermArr['ommaterialsample']['condition']['l'];
					?>
					<select name="ms_condition">
						<option value="">-------</option>
						<?php
						foreach($controlTermArr['ommaterialsample']['condition']['v'] as $t){
							echo '<option '.($msArr && $msArr['condition'] == $t?'selected':'').'>'.$t.'</option>';
						}
						?>
					</select>
					<?php
				}
				else{
					?>
					<input type="text" name="ms_condition" value="<?php echo isset($msArr['condition'])?$msArr['condition']:''; ?>" />
					<?php
				}
				?>
			</div>
			<div id="smDispositionDiv">
				<label><?php echo $MS_DISPOSITION_LABEL; ?></label>
				<?php
				if(isset($controlTermArr['ommaterialsample']['disposition'])){
					$limitToList = $controlTermArr['ommaterialsample']['disposition']['l'];
					?>
					<select name="ms_disposition">
						<option value="">-------</option>
						<?php
						foreach($controlTermArr['ommaterialsample']['disposition']['v'] as $t){
							echo '<option '.($msArr && $msArr['disposition'] == $t?'selected':'').'>'.$t.'</option>';
						}
						?>
					</select>
					<?php
				}
				else{
					?>
					<input type="text" name="ms_disposition" value="<?php echo isset($msArr['disposition'])?$msArr['disposition']:''; ?>" />
					<?php
				}
				?>
			</div>
			<div id="smPreservationTypeDiv">
				<label><?php echo $MS_PRESERVATION_TYPE_LABEL; ?></label>
				<?php
				if(isset($controlTermArr['ommaterialsample']['preservationType'])){
					$limitToList = $controlTermArr['ommaterialsample']['preservationType']['l'];
					?>
					<select name="ms_preservationType">
						<option value="">-------</option>
						<?php
						foreach($controlTermArr['ommaterialsample']['preservationType']['v'] as $t){
							echo '<option '.($msArr && $msArr['preservationType'] == $t?'selected':'').'>'.$t.'</option>';
						}
						?>
					</select>
					<?php
				}
				else{
					?>
					<input type="text" name="ms_preservationType" value="<?php echo isset($msArr['preservationType'])?$msArr['preservationType']:''; ?>" />
					<?php
				}
				?>
			</div>
			<div id="smPreparationDetailsDiv">
				<label><?php echo $MS_PRESERVATION_DETAILS_LABEL; ?></label>
				<input type="text" name="ms_preparationDetails" value="<?php echo isset($msArr['preparationDetails'])?$msArr['preparationDetails']:''; ?>" />
			</div>
			<div id="smPreparationDateDiv">
				<label><?php echo $MS_PRESERVATION_DATE_LABEL; ?></label>
				<input type="date" name="ms_preparationDate" value="<?php echo isset($msArr['preparationDate'])?$msArr['preparationDate']:''; ?>" />
			</div>
			<div id="smPreparedByUidDiv">
				<label><?php echo $MS_PREPARED_BY_LABEL; ?></label>
				<input type="text" name="ms_preparedBy" value="<?php echo isset($msArr['preparedBy'])?$msArr['preparedBy']:''; ?>" />
				<input type="hidden" name="ms_preparedByUid" value="<?php echo isset($msArr['preparedByUid'])?$msArr['preparedByUid']:''; ?>" />
			</div>
			<div id="smIndividualCountDiv">
				<label><?php echo $MS_INDIVIDUAL_COUNT_LABEL; ?></label>
				<input type="text" name="ms_individualCount" value="<?php echo isset($msArr['individualCount'])?$msArr['individualCount']:''; ?>" />
			</div>
			<div id="smSampleSizeDiv">
				<label><?php echo $MS_SAMPLE_SIZE_LABEL; ?></label>
				<input type="text" name="ms_sampleSize" value="<?php echo isset($msArr['sampleSize'])?$msArr['sampleSize']:''; ?>" />
			</div>

			<div id="smStorageLocationDiv">
				<label><?php echo $MS_STORAGE_LOCATION_LABEL; ?></label>
				<?php
				if(isset($controlTermArr['ommaterialsample']['storageLocation'])){
					$limitToList = $controlTermArr['ommaterialsample']['storageLocation']['l'];
					?>
					<select name="ms_storageLocation">
						<option value="">-------</option>
						<?php
						foreach($controlTermArr['ommaterialsample']['storageLocation']['v'] as $t){
							echo '<option '.($msArr && $msArr['storageLocation'] == $t?'selected':'').'>'.$t.'</option>';
						}
						?>
					</select>
					<?php
				}
				else{
					?>
					<input type="text" name="ms_storageLocation" value="<?php echo isset($msArr['storageLocation'])?$msArr['storageLocation']:''; ?>" />
					<?php
				}
				?>
			</div>
			<div id="smRemarksDiv">
				<label><?php echo $MS_REMARKS_LABEL; ?></label>
				<input type="text" name="ms_remarks" value="<?php echo isset($msArr['remarks'])?$msArr['remarks']:''; ?>" />
			</div>
			</div>
				<input name="occid" type="hidden" value="<?php echo $msArr['occid']; ?>" />
				<input name="matSampleID" type="hidden" value="<?php echo $msArr['matSampleID']; ?>" />
				<input name="collid" type="hidden" value="<?php echo $msArr['collid']; ?>" />
				<button name="formsubmit" type="submit" value="saveMatSample">Save Changes</button>
			<div>
		</div>
	</form>
</fieldset>