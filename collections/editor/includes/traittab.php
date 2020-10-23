<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceAttributes.php');
header("Content-Type: text/html; charset=".$CHARSET);

$occid = $_GET['occid'];
$occIndex = $_GET['occindex'];
$collid = isset($_GET['collid'])?$_GET['collid']:'';

$attrManager = new OccurrenceAttributes();
$attrManager->setOccid($occid);

$isEditor = 0;
if($IS_ADMIN || ($collid && array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"]))){
	$isEditor = 1;
}
elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollEditor"])){
	$isEditor = 2;
}
elseif($attrManager->getObserverUid() == $SYMB_UID){
	//Users can edit their own records
	$isEditor = 2;
}

if($isEditor){
	if(isset($_GET['deltraitid']) && $_GET['deltraitid']){
		$attrManager->deleteAttributes($_GET['deltraitid']);
	}
}
?>
<script src="../../js/symb/collections.traitattr.js?ver=8" type="text/javascript"></script>
<script type="text/javascript">

	function verifySubmitForm(f){

		return true;
	}

	function submitEditForm(butElem){
		var f = butElem.form;
		var action = butElem.value;
		var continueProcessing = false;
		var stateJson = {};

		$("form[name='"+f.name+"'] input[name^='traitid']").each(function(index,data) {
			if($(this).prop('checked')){
				if($(this).attr('name') in stateJson){
					stateJson[$(this).attr('name')].push($(this).val());
				}
				else{
					stateJson[$(this).attr('name')] = [$(this).val()];
				}
				continueProcessing = true;
			}
		});
		if(!continueProcessing){
			alert("No traits have been selected");
			return false;
		}
		if(action == "deleteTraitCoding"){
			if(!confirm('Are you sure you want to delete this trait coding?')) return false;
		}
		var traitIdStr = f.traitid.value;
		$("#msgDiv-"+traitIdStr).text('applying action...');
		$("#msgDiv-"+traitIdStr).css('color', 'orange');
		//alert("collid"+f.collid.value+"&occid="+f.occid.value+"&traitID="+traitIdStr+"&submitAction="+action+"&source="+f.source.value+"&notes="+f.notes.value+"&setStatus="+f.setstatus.value+"&stateData="+JSON.stringify(stateJson));
		$.ajax({
			type: "POST",
			url: "rpc/editorTraitHandler.php",
			data: { collid: f.collid.value, occid: f.occid.value, traitID: traitIdStr, submitAction: action, source: f.source.value, notes: f.notes.value, setStatus: f.setstatus.value, stateData: JSON.stringify(stateJson) }
		}).done(function( retStatus ) {
			if(retStatus == 1){
				$("#msgDiv-"+traitIdStr).css('color', 'green');
				$("#msgDiv-"+traitIdStr).text('data saved!');
			}
			else if(retStatus == 2){
				$("form[name='"+f.name+"'] input[name^='traitid']").each(function(index,data) {
					$(this).prop('checked',false);
				});
				$("#msgDiv-"+traitIdStr).css('color', 'green');
				$("#msgDiv-"+traitIdStr).text('data deleted!');
			}
			else{
				$("#msgDiv-"+traitIdStr).css('color', 'red');
				$("#msgDiv-"+traitIdStr).text('ERROR saving data: '+retStatus);
			}
		});
	}

</script>
<div id="traitdiv" style="width:795px;">
	<?php
	$traitArr = $attrManager->getTraitArr();
	foreach($traitArr as $tID => $tArr){
		if(isset($tArr['states'])){
			foreach($tArr['states'] as $sID => $sArr){
				if(isset($sArr['coded'])){
					$codedTraitArr[$tID] = $tArr;
					break;
				}
			}
		}
	}
	if($traitArr){
		foreach($traitArr as $traitID => $traitData){
			if(!isset($traitData['dependentTrait'])){
				$statusCode = 0;
				$notes = '';
				$source = '';
				foreach($traitData['states'] as $id => $stArr){
					if(isset($stArr['statuscode']) && $stArr['statuscode']) $statusCode = $stArr['statuscode'];
					if(isset($stArr['notes']) && $stArr['notes']) $notes = $stArr['notes'];
					if(isset($stArr['source']) && $stArr['source']) $source = $stArr['source'];
				}
				?>
				<fieldset style="margin-top:20px">
					<legend><b>Trait: <?php echo $traitData['name']; ?></b></legend>
					<div style="float:right">
						<div style="margin:0px 3px;float:right" title="Hard refresh of page">
							<form name="refreshform" method="post" action="occurrenceeditor.php" >
								<input name="occid" type="hidden" value="<?php echo $occid; ?>" />
								<?php
								if($occIndex) echo '<input name="occindex" type="hidden" value="'.$occIndex.'" />';
								?>
								<input name="tabtarget" type="hidden" value="4" />
								<input type="image" src="../../images/refresh.png" style="width:14px;vertical-align: middle;" />
							</form>
						</div>
						<div class="trianglediv" style="margin:4px 3px;float:right;cursor:pointer" onclick="setAttributeTree(this)" title="Toggle attribute tree open/close">
							<img class="triangleright" src="../../images/triangleright.png" style="" />
							<img class="triangledown" src="../../images/triangledown.png" style="display:none" />
						</div>
					</div>
					<form name="submitform<?php echo '-'.$traitID; ?>" method="post" action="occurrenceeditor.php" onsubmit="return false">
						<div class="traitDiv" style="margin-left:5px;float:left">
							<?php
							$attrManager->echoFormTraits($traitID);
							?>
						</div>
						<div style="clear:both;padding:10px 5px;">
							<div >
								Notes:
								<input name="notes" type="text" style="width:300px" value="<?php echo $notes; ?>" />
							</div>
							<div style="margin:10px 0px">
								Source:
								<select name="source">
									<option value=""></option>
									<?php
									$sourceControlArr = $attrManager->getSourceControlledArr($source);
									foreach($sourceControlArr as $sourceTerm){
										echo '<option '.($source==$sourceTerm?'selected':'').'>'.$sourceTerm.'</option>';
									}
									?>
								</select>
							</div>
							<div style="margin-left:5;">
								Status:
								<select name="setstatus">
									<option value="0">Not reviewed</option>
									<option value="5" <?php echo ($statusCode=='5'?'selected':''); ?>>Expert Needed</option>
									<option value="10" <?php echo ($statusCode=='10'?'selected':''); ?>>Reviewed</option>
								</select>
							</div>
							<div style="margin:20px;float:left">
								<input name="occid" type="hidden" value="<?php echo $occid; ?>" />
								<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
								<input name="occindex" type="hidden" value="<?php echo $occIndex; ?>" />
								<input name="traitid" type="hidden" value="<?php echo $traitID; ?>" />
								<input name="tabtarget" type="hidden" value="4" />
								<button type="button" value="editTraitCoding" onclick="submitEditForm(this); return false">Save Edits</button>
								<span id="msgDiv-<?php echo $traitID; ?>"></span>
							</div>
							<div style="margin:20px;float:right;">
								<button type="button" value="deleteTraitCoding" style="border:1px solid red;"  onclick="submitEditForm(this); return false">Delete Coding</button>
							</div>
						</div>
					</form>
				</fieldset>
				<?php
			}
		}
	}
	?>
</div>