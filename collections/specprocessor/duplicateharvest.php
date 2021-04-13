<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecProcDuplicates.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$evaluationDate = array_key_exists('evaldate',$_POST)?$_POST['evaldate']:'';
$processingStatus = array_key_exists('processingstatus',$_POST)?$_POST['processingstatus']:'';
$limit = array_key_exists('limit',$_POST)?$_POST['limit']:1000;
$action = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/cleaning/dupematching.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

//Sanitation
if(!is_numeric($collid)) $collid = 0;
if(!preg_match('/^\d{4}\/\d{2}\/\d{2}$/',$evaluationDate)) $evaluationDate = '';
if(!in_array($processingStatus,array('stage1','stage2','stage3','unprocessed'))) $processingStatus = '';
if(!is_numeric($limit)) $limit = 1000;
$action = filter_var($action,FILTER_SANITIZE_STRING);

$dupeManager = new SpecProcDuplicates();
if($collid) $dupeManager->setCollID($collid);
$collMetaArr = $dupeManager->getCollMetaArr();

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))) $isEditor = 1;

$occurArr = array();
if($action == ''){
	//$dupArr = $dupeManager->buildDuplicateArr($evaluationDate,$limit);
}

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE; ?> - Duplicate Georeferencing</title>
	<?php
	$activateJQuery = false;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script type="text/javascript">

	</script>
	<style type="text/css">
		#page-title{ margin-bottom: 10px; }
		fieldset{ margin: 10px; display: inline-block; min-width: 400px }
		legend{ font-weight: bold; }
		.fieldGroup{ clear:both; margin: 3px 0px; }
		.fieldLabel{ margin-left: 3px; }
		button{ margin: 10px }
		.source-row{  }
		.dupe-row{ background-color: lightgray; }
		.edit-cell{ border-color: green; }
		.source-cell{ border-color: yellow; }
	</style>
</head>
<body style="margin-left:0px;margin-right:0px">
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?emode=1&collid=<?php echo $collid; ?>">Collection Management</a> &gt;&gt;
		<b>Batch harvesting from Duplicates</b>
	</div>
	<!-- inner text -->
	<div id="innertext" style="background-color:white;">
		<?php
		echo '<div id="page-title">'.$collMetaArr[$collid]['name'].' ('.$collMetaArr[$collid]['collcode'].')</div>';
		if($isEditor){
			?>
			<fieldset>
				<legend>Staging Variables</legend>
				<form name="stagingForm" method="post" action="duplicateharvest.php">
					<div class="fieldGroup">
						<div class="fieldLabel" style="float:left">Target Fields: </div>
						<fieldset style="float:left">
							<input name="fieldtarget" type="radio" value="all" /> All fields<br/>
							<input name="fieldtarget" type="radio" value="georef" /> Georeference fields
						</fieldset>
					</div>
					<div class="fieldGroup">
						<div class="fieldLabel" style="float:left">Matching method: </div>
						<fieldset style="float:left">
							<input name="fieldtarget" type="radio" value="dupe" /> Duplicate specimen tables<br/>
							<input name="fieldtarget" type="radio" value="exsiccati" /> Exsiccatae tables
						</fieldset>
					</div>
					<div class="fieldGroup">
						<div class="fieldLabel" style="float:left">Records not evaluated since: </div>
						<div style="float:left">
							<input name="evaldate" type="date" value="" />
						</div>
					</div>
					<div class="fieldGroup">
						<div class="fieldLabel" style="float:left">Processing status: </div>
						<div style="float:left">
							<select name="processingstatus">
								<option value="">All Records</option>
								<option value="stage1">Stage 1</option>
								<option value="stage2">Stage 2</option>
								<option value="stage3">Stage 3</option>
								<option value="unprocessed" selected>Unprocessed</option>
							</select>
						</div>
					</div>
					<div class="fieldGroup">
						<span class="fieldLabel">Record limit: </span>
						<input name="targetFields" type="text" value="100" />
					</div>
					<div class="buttonDiv">
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<button name="formsubmit" type="submit" value="buildDuplicateArr">Build List</button>
					</div>
				</form>
			</fieldset>
			<?php
			if($action == 'buildDuplicateArr'){
				$dupArr = $dupeManager->buildDuplicateArr($evaluationDate,$limit);
				$activeFieldArr = $dupeManager->getActiveFieldArr();
				?>
				<div>
					<form name="dupeUpdateForm" target="duplicateharvest.php" method="post">
						<table id="dupe-table">
							<tr>
								<th><input name="all" type="checkbox" /> title="Select All"</th>
								<th>occid</th>
								<th>Collection<br/>Code</th>
								<th>Catalog<br/>Number</th>
								<?php
								foreach($activeFieldArr as $fieldName => $code){
									echo '<th>'.$fieldName.'</th>';
								}
								?>
							</tr>
							<?php
							foreach($dupArr as $occid => $dupeArr){
								//Output subject array
								echo '<tr class="source-row">';
								echo '<td><input name="all" type="checkbox" /></td>';
								echo '<td><a href="../editor/occurrenceeditor.php?occid='.$occid.'" target="_blank">'.$occid.'</a></td>';
								echo '<td></td>';
								echo '<td>'.$dupeArr[0]['catalogNumber']['v'].'</td>';
								foreach($activeFieldArr as $fieldName => $code){
									$previousValue = $dupeArr[0][$fieldName]['v'];
									$suggestedValue = $dupeArr[0][$fieldName]['p'];
									echo '<td class="'.($previousValue!=$suggestedValue?'edit-cell':'').'" title="Current value: '.htmlentities($previousValue).'">';
									if($previousValue != $suggestedValue) echo '<input name="'.$occid.'-'.$fieldName.'" value="'.htmlentities($suggestedValue).'" />';
									else echo $previousValue;
									echo '</td>';
								}
								echo '</tr>';
								//Output duplicate records
								for($i=1; $i < count($dupeArr); $i++){
									echo '<tr class="dupe-row">';
									echo '<td></td>';
									echo '<td><a href="../individual/index.php?occid='.$occid.'" target="_blank">'.$occid.'</a></td>';
									echo '<td>'.$dupeArr[0]['collcode']['v'].'</td>';
									echo '<td>'.$dupeArr[0]['catalogNumber']['v'].'</td>';
									foreach($activeFieldArr as $fieldName => $code){
										$classCode = '';
										if(isset($dupeArr[$i][$fieldName]['s'])) $classCode = 'source-cell';
										echo '<td class="'.$classCode.'">';
										echo htmlentities($dupeArr[$i][$fieldName]['v']);
										echo '</td>';
									}
									echo '</td>';
								}
							}
							?>
						</table>
					</form>
				</div>
				<?php
			}
		}
		else{
			echo '<h2>You are not authorized to access this page</h2>';
		}
		?>
	</div>
</body>
</html>