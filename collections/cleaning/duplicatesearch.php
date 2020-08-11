<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCleaner.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';
$start = array_key_exists('start',$_REQUEST)?$_REQUEST['start']:0;
$limit = array_key_exists('limit',$_REQUEST)?$_REQUEST['limit']:1000;

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/cleaning/duplicatesearch.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

//Sanitation
if(!is_numeric($collid)) $collid = 0;
if($action && !preg_match('/^[a-zA-Z0-9\s_]+$/',$action)) $action = '';
if(!is_numeric($start)) $start = 0;
if(!is_numeric($limit)) $limit = 1000;

$cleanManager = new OccurrenceCleaner();
if($collid) $cleanManager->setCollId($collid);
$collMap = current($cleanManager->getCollMap());

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || (array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS["CollAdmin"])) || ($collMap['colltype'] == 'General Observations')){
	$isEditor = 1;
}

//If collection is a general observation project, limit to User
if($collMap['colltype'] == 'General Observations'){
	$cleanManager->setObsUid($SYMB_UID);
}

$limit = ini_get('max_input_vars')*0.2;
if(!$limit || $limit > 1000) $limit = 1000;

$dupArr = array();
if($action == 'listdupscatalog'){
	$dupArr = $cleanManager->getDuplicateCatalogNumber('cat',$start,$limit);
}
if($action == 'listdupsothercatalog'){
	$dupArr = $cleanManager->getDuplicateCatalogNumber('other',$start,$limit);
}
elseif($action == 'listdupsrecordedby'){
	$dupArr = $cleanManager->getDuplicateCollectorNumber($start);
}

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE; ?> Occurrence Cleaner</title>
  <?php
    $activateJQuery = false;
    if(file_exists($SERVER_ROOT.'/includes/head.php')){
      include_once($SERVER_ROOT.'/includes/head.php');
    }
    else{
      echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
      echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
      echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
    }
  ?>
  <style type="text/css">
  table.styledtable td { white-space: nowrap; }
  </style>
	<script type="text/javascript">
		function validateMergeForm(f){
			var dbElements = document.getElementsByName("dupid[]");
			for(i = 0; i < dbElements.length; i++){
				var dbElement = dbElements[i];
				if(dbElement.checked) return true;
			}
		   	alert("Please select specimens to be merged!");
	      	return false;
		}

		function selectAllDuplicates(f){
			var boxesChecked = true;
			if(!f.selectalldupes.checked){
				boxesChecked = false;
			}
			var dbElements = document.getElementsByName("dupid[]");
			for(i = 0; i < dbElements.length; i++){
				dbElements[i].checked = boxesChecked;
			}
		}

		function batchSwitchTargetSpecimens(cbElem){
			cbElem.checked = false;
			var dbElements = document.getElementsByTagName("input");
			//var dbElements = $("input[type='radio']").val();
			var elemName = '';
			for(i = 0; i < dbElements.length; i++){
				if(dbElements[i].type == "radio"){
					if(dbElements[i].checked == false && elemName != dbElements[i].name){
						dbElements[i].checked = true;
						elemName = dbElements[i].name;
					}
				}
			}
		}
	</script>
</head>
<body style="margin-left:0px;margin-right:0px">
	<div class='navpath'>
		<a href="../../index.php">Home</a> &gt;&gt;
		<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
		<a href="index.php?collid=<?php echo $collid; ?>">Cleaning Module Index</a> &gt;&gt;
		<b>Duplicate Occurrences</b>
	</div>

	<!-- inner text -->
	<div id="innertext" style="background-color:white;">
		<?php
		if($isEditor){
			if($IS_ADMIN && $limit < 900) echo '<div><span style="color:orange">NOTICE to SuperAdmin:</span> You can increase upper limit of the number of records that this form can process by increasing the max_input_vars variable within your PHP configuration file. Increasing this variable to 4500 will set the upper limit of this form to 1000 duplicate clusters.</div>';
			if($action == 'listdupscatalog' || $action == 'listdupsothercatalog' || $action == 'listdupsrecordedby'){
				//Look for duplicate catalognumbers
				if($dupArr){
					$recCnt = count($dupArr);
					//Build table
					?>
					<div style="margin-bottom:10px;">
						<b>Use the checkboxes to select the records you would like to merge, and the radio buttons to select which target record to merge into.</b>
					</div>
					<form name="mergeform" action="duplicatesearch.php" method="post" onsubmit="return validateMergeForm(this);">
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<?php
						if($recCnt > $limit){
							$href = 'duplicatesearch.php?collid='.$collid.'&action='.$action.'&start='.($start+$limit);
							echo '<div style="float:right;"><a href="'.$href.'"><b>NEXT '.$limit.' RECORDS &gt;&gt;</b></a></div>';
						}
						echo '<div style="float:left;margin-bottom:4px;margin-left:15px;"><input name="action" type="submit" value="Merge Duplicate Records" /></div>';
						echo '<div style="float:left;margin-left:15px;"><b>'.($start+1).' to '.($start+$recCnt).' Duplicate Clusters </b></div>';
						?>
						<div style="clear: both">
							<table class="styledtable" style="font-family:Arial;font-size:12px;">
								<tr>
									<th style="width:40px;">ID</th>
									<th style="width:20px;"><input name="selectalldupes" type="checkbox" title="Select/Deselect All" onclick="selectAllDuplicates(this.form)" /></th>
									<th><input type="checkbox" name="batchswitch" onclick="batchSwitchTargetSpecimens(this)" title="Batch switch target specimens" /></th>
									<th style="width:40px;">Catalog Number</th>
									<th style="width:40px;">Other Catalog Numbers</th>
									<th>Scientific Name</th>
									<th>Collector</th>
									<th>Collection Number</th>
									<th>Associated Collectors</th>
									<th>Collection Date</th>
									<th>Verbatim Date</th>
									<th>Country</th>
									<th>State</th>
									<th>County</th>
									<th>Locality</th>
									<th>Date Last Modified</th>
								</tr>
								<?php
								$setCnt = 0;
								foreach($dupArr as $dupKey => $occArr){
									$setCnt++;
									$first = true;
									foreach($occArr as $occId => $occArr){
										echo '<tr '.(($setCnt % 2) == 1?'class="alt"':'').'>';
										echo '<td><a href="../editor/occurrenceeditor.php?occid='.$occId.'" target="_blank">'.$occId.'</a></td>'."\n";
										echo '<td><input name="dupid[]" type="checkbox" value="'.$dupKey.':'.$occId.'" /></td>'."\n";
										echo '<td><input name="dup'.$dupKey.'target" type="radio" value="'.$occId.'" '.($first?'checked':'').'/></td>'."\n";
										echo '<td>'.$occArr['catalognumber'].'</td>'."\n";
										echo '<td>'.$occArr['othercatalognumbers'].'</td>'."\n";
										echo '<td>'.$occArr['sciname'].'</td>'."\n";
										echo '<td>'.$occArr['recordedby'].'</td>'."\n";
										echo '<td>'.$occArr['recordnumber'].'</td>'."\n";
										echo '<td>'.$occArr['associatedcollectors'].'</td>'."\n";
										echo '<td>'.$occArr['eventdate'].'</td>'."\n";
										echo '<td>'.$occArr['verbatimeventdate'].'</td>'."\n";
										echo '<td>'.$occArr['country'].'</td>'."\n";
										echo '<td>'.$occArr['stateprovince'].'</td>'."\n";
										echo '<td>'.$occArr['county'].'</td>'."\n";
										echo '<td>'.$occArr['locality'].'</td>'."\n";
										echo '<td>'.$occArr['datelastmodified'].'</td>'."\n";
										echo '</tr>';
										$first = false;
									}
								}
								?>
							</table>
						</div>
						<div style="margin:15px;">
							<input name="action" type="submit" value="Merge Duplicate Records" />
						</div>
					</form>
					<?php
				}
				else{
					?>
					<div style="margin:25px;font-weight:bold;font-size:120%;">
						There are no duplicate catalog numbers!
					</div>
					<?php
				}
			}
			elseif($action == 'Merge Duplicate Records'){
				?>
				<ul>
					<li>Duplicate merging process started</li>
					<?php
					$dupArr = array();
					foreach($_POST['dupid'] as $v){
						$vArr = explode(':',$v);
						if(count($vArr) > 1){
							$target = $_POST['dup'.$vArr[0].'target'];
							if($target != $vArr[1]) $dupArr[$target][] = $vArr[1];
						}
					}
					$cleanManager->mergeDupeArr($dupArr);
					?>
					<li>Done!</li>
				</ul>
				<div style="margin-top:10px">
					<?php
					if((count($dupArr)+2)>$limit){
						?>
							<div>
								<a href="index.php?collid=<?php echo $collid; ?>">Return to duplicate merge form</a>
							</div>
							<?php
						}
					?>
					<div>
						<a href="index.php?collid=<?php echo $collid; ?>">Return to main menu</a>
					</div>
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