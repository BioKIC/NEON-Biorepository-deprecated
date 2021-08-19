<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorMaterialSample.php');
include_once($SERVER_ROOT.'/collections/editor/includes/config/materialSampleVars.php');
header("Content-Type: text/html; charset=".$CHARSET);

$occid = $_REQUEST['occid'];
$occIndex = $_REQUEST['occindex'];
$collid = isset($_REQUEST['collid'])?$_REQUEST['collid']:'';

$materialSampleManager = new OccurrenceEditorMaterialSample();

//Sanitation
if(!is_numeric($occid)) $occid = 0;
if(!is_numeric($collid)) $collid = 0;
$materialSampleManager->cleanFormData($_POST);

$materialSampleManager->setOccid($occid);

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif($collId && array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])) $isEditor = true;
elseif(array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollEditor'])) $isEditor = true;

$materialSampleArr = $materialSampleManager->getMaterialSampleArr();
$controlTermArr = $materialSampleManager->getMSTypeControlValues();
?>
<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
<script>
	$(document).ready(function() {
		$("#ms_preparedBy").autocomplete({
			source: function( request, response ) {
				$.getJSON( "rpc/getUsers.php", { term: request.term, collid: $("#collid").val() }, response );
			},
			minLength: 2,
			autoFocus: true,
			select: function( event, ui ) {
				if(ui.item) $("#ms_preparedByUid").val(ui.item.id);
			}
		});
	});
</script>
<link href="<?php echo $CLIENT_ROOT; ?>/css/jquery-ui.css?ver=1" type="text/css" rel="stylesheet" />
<link href="includes/config/materialsample.css?ver=1" type="text/css" rel="stylesheet" />
<style type="text/css">
	botton { margin: 10px; }
</style>
<div style="width:795px;">
	<div style="clear:both; margin: 20px;">
		See <a href="https://tools.gbif.org/dwca-validator/extension.do?id=http://data.ggbn.org/schemas/ggbn/terms/MaterialSample" target="_blank">GGBN Material Sample Extension</a> documentation
	</div>
	<?php
	if($isEditor){
		$msArr = array();
		do{
			$matSampleID = 0;
			if($msArr) $matSampleID = $msArr['matSampleID'];
			?>
			<fieldset>
				<legend>Material Sample</legend>
				<form name="matSample<?php echo ($msArr?'Edit-'.$matSampleID:'Add') ?>Form" action="occurrenceeditor.php" method="post">
					<div style="clear:both">
						<div class="fieldBlock" id="smSampleTypeDiv">
							<label><?php echo $MS_TYPE_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['sampleType'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<?php
								if(isset($controlTermArr['ommaterialsample']['sampleType'])){
									$limitToList = $controlTermArr['ommaterialsample']['sampleType']['l'];
									?>
									<select name="ms_sampleType" required>
										<option value="">-------</option>
										<?php
										foreach($controlTermArr['ommaterialsample']['sampleType']['t'] as $t){
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
							</span>
						</div>
						<div class="fieldBlock" id="smCatalogNumberDiv">
							<label><?php echo $MS_CATALOG_NUMBER_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['catalogNumber'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<input type="text" name="ms_catalogNumber" value="<?php echo isset($msArr['catalogNumber'])?$msArr['catalogNumber']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smGuidDiv">
							<label><?php echo $MS_GUID_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['guid'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<input type="text" name="ms_guid" value="<?php echo isset($msArr['guid'])?$msArr['guid']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smConditionDiv">
							<label><?php echo $MS_CONDITION_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['condition'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<?php
								if(isset($controlTermArr['ommaterialsample']['condition'])){
									$limitToList = $controlTermArr['ommaterialsample']['condition']['l'];
									?>
									<select name="ms_condition">
										<option value="">-------</option>
										<?php
										foreach($controlTermArr['ommaterialsample']['condition']['t'] as $t){
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
							</span>
						</div>
						<div class="fieldBlock" id="smDispositionDiv">
							<label><?php echo $MS_DISPOSITION_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['disposition'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<?php
								if(isset($controlTermArr['ommaterialsample']['disposition'])){
									$limitToList = $controlTermArr['ommaterialsample']['disposition']['l'];
									?>
									<select name="ms_disposition">
										<option value="">-------</option>
										<?php
										foreach($controlTermArr['ommaterialsample']['disposition']['t'] as $t){
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
							</span>
						</div>
						<div class="fieldBlock" id="smPreservationTypeDiv">
							<label><?php echo $MS_PRESERVATION_TYPE_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['preservationType'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<?php
								if(isset($controlTermArr['ommaterialsample']['preservationType'])){
									$limitToList = $controlTermArr['ommaterialsample']['preservationType']['l'];
									?>
									<select name="ms_preservationType">
										<option value="">-------</option>
										<?php
										foreach($controlTermArr['ommaterialsample']['preservationType']['t'] as $t){
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
							</span>
						</div>
						<div class="fieldBlock" id="smPreparationDateDiv">
							<label><?php echo $MS_PRESERVATION_DATE_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['preparationDate'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<input type="date" name="ms_preparationDate" value="<?php echo isset($msArr['preparationDate'])?$msArr['preparationDate']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smPreparedByUidDiv">
							<label><?php echo $MS_PREPARED_BY_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['preparedBy'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<input id="ms_preparedBy" name="ms_preparedBy" type="text" value="<?php echo isset($msArr['preparedBy'])?$msArr['preparedBy']:''; ?>" />
								<input id="ms_preparedByUid" name="ms_preparedByUid" type="hidden" value="<?php echo isset($msArr['preparedByUid'])?$msArr['preparedByUid']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smPreparationDetailsDiv">
							<label><?php echo $MS_PRESERVATION_DETAILS_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['preparationDetails'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<input type="text" name="ms_preparationDetails" value="<?php echo isset($msArr['preparationDetails'])?$msArr['preparationDetails']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smIndividualCountDiv">
							<label><?php echo $MS_INDIVIDUAL_COUNT_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['individualCount'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<input type="text" name="ms_individualCount" value="<?php echo isset($msArr['individualCount'])?$msArr['individualCount']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smSampleSizeDiv">
							<label><?php echo $MS_SAMPLE_SIZE_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['sampleSize'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<input type="text" name="ms_sampleSize" value="<?php echo isset($msArr['sampleSize'])?$msArr['sampleSize']:''; ?>" />
							</span>
						</div>
						<div class="fieldBlock" id="smStorageLocationDiv">
							<label><?php echo $MS_STORAGE_LOCATION_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['storageLocation'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<?php
								if(isset($controlTermArr['ommaterialsample']['storageLocation'])){
									$limitToList = $controlTermArr['ommaterialsample']['storageLocation']['l'];
									?>
									<select name="ms_storageLocation">
										<option value="">-------</option>
										<?php
										foreach($controlTermArr['ommaterialsample']['storageLocation']['t'] as $t){
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
							</span>
						</div>
						<div class="fieldBlock" id="smRemarksDiv">
							<label><?php echo $MS_REMARKS_LABEL; ?>: </label>
							<?php
							if($matSampleID) echo '<span class="display-elem'.$matSampleID.'">'.$msArr['remarks'].'</span>';
							?>
							<span class="edit-elem<?php echo $matSampleID; ?>">
								<input type="text" name="ms_remarks" value="<?php echo isset($msArr['remarks'])?$msArr['remarks']:''; ?>" />
							</span>
						</div>
						<div style="clear:both;">
							<input name="occid" type="hidden" value="<?php echo (isset($msArr['occid'])?$msArr['occid']:''); ?>" />
							<input name="matSampleID" type="hidden" value="<?php echo $matSampleID; ?>" />
							<input id="collid" name="collid" type="hidden" value="<?php echo (isset($msArr['collid'])?$msArr['collid']:''); ?>" />
							<input name="occindex" type="hidden" value="<?php echo $occIndex; ?>" />
							<input name="tabindex" type="hidden" value="3" />
							<?php
							if($msArr) echo '<button name="submitaction" type="submit" value="saveMatSample">Save Changes</button>';
							else echo '<button name="submitaction" type="submit" value="saveMatSample">Add Record</button>';
							?>
						</div>
					</div>
				</form>
			</fieldset>
			<?php
		}while($msArr = array_pop($materialSampleArr));
	}
	?>
</div>