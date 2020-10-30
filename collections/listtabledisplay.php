<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/collections/list.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/OccurrenceListManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$page = array_key_exists('page',$_REQUEST)?$_REQUEST['page']:1;
$tableCount= array_key_exists('tablecount',$_REQUEST)?$_REQUEST['tablecount']:1000;
$sortField1 = array_key_exists('sortfield1',$_REQUEST)?$_REQUEST['sortfield1']:'collectionname';
$sortField2 = array_key_exists('sortfield2',$_REQUEST)?$_REQUEST['sortfield2']:'';
$sortOrder = array_key_exists('sortorder',$_REQUEST)?$_REQUEST['sortorder']:'';

//Sanitation
if(!is_numeric($page) || $page < 1) $page = 1;
if(!is_numeric($tableCount)) $tableCount = 1000;
$sortField1 = strip_tags($sortField1);
$sortField2 = strip_tags($sortField2);
$sortOrder = strip_tags($sortOrder);

$collManager = new OccurrenceListManager();
$searchVar = $collManager->getQueryTermStr();
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title><?php echo $DEFAULT_TITLE; ?> Collections Search Results Table</title>
	<style type="text/css">
		table.styledtable td {
			white-space: nowrap;
		}
	</style>
	<?php
	$activateJQuery = true;
	if(file_exists($SERVER_ROOT.'/includes/head.php')){
		include_once($SERVER_ROOT.'/includes/head.php');
	}
	else{
		echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	?>
	<script src="../js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		<?php include_once($SERVER_ROOT.'/includes/googleanalytics.php'); ?>
	</script>
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
			if($searchVar){
				?>
				sessionStorage.querystr = "<?php echo $searchVar; ?>";
				<?php
			}
			?>
		});
	</script>
	<script src="../js/symb/collections.list.js?ver=9" type="text/javascript"></script>
</head>
<body style="margin-left: 0px; margin-right: 0px;background-color:white;">
	<div id="">
		<div style="width:850px;margin-bottom:5px;">
			<div style="float:right;">
				<!--
				<div style="float:left">
					<button class="icon-button" onclick="$('.datasetDiv').toggle();" title="Dataset Management">
						<img src="../images/dataset.png" style="width:15px;" />
					</button>
				</div>
				-->
				<form action="list.php" method="post" style="float:left">
					<button class="ui-button ui-widget ui-corner-all" style="margin:5px;padding:5px;" title="<?php echo (isset($LANG['LIST_DISPLAY'])?$LANG['LIST_DISPLAY']:'List Display'); ?>">
						<img src="../images/list.png" style="width:15px; height:15px" />
					</button>
					<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
				</form>
				<form action="download/index.php" method="post" style="float:left" onsubmit="targetPopup(this)">
					<button class="ui-button ui-widget ui-corner-all" style="margin:5px;padding:5px;" title="<?php echo $LANG['DOWNLOAD_SPECIMEN_DATA']; ?>">
						<img src="../../images/dl2.png" srcset="../images/download.svg" class="svg-icon" style="width:15px; height:15px" />
					</button>
					<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
					<input name="dltype" type="hidden" value="specimen" />
				</form>
				<div style="float:left">
					<button class="ui-button ui-widget ui-corner-all" style="margin:5px;padding:5px;" onclick="copyUrl()" title="Copy URL to Clipboard">
						<img src="../../images/dl2.png" srcset="../images/link.svg" class="svg-icon" style="width:15px; height:15px" />
					</button>
				</div>
			</div>
			<fieldset style="padding:5px;width:650px;">
				<legend><b>Sort Results</b></legend>
				<form name="sortform" action="listtabledisplay.php" method="post">
					<div style="float:left;">
						<b>Sort By:</b>
						<select name="sortfield1">
							<?php
							$sortFields = array('c.collectionname' => 'Collection', 'o.catalogNumber' => 'Catalog Number', 'o.family' => 'Family', 'o.sciname' => 'Scientific Name', 'o.recordedBy' => 'Collector',
								'o.recordNumber' => 'Number', 'o.eventDate' => 'Event Date', 'o.country' => 'Country', 'o.StateProvince' => 'State/Province', 'o.county' => 'County', 'o.minimumElevationInMeters' => 'Elevation');
							foreach($sortFields as $k => $v){
								echo '<option value="'.$k.'" '.($k==$sortField1?'SELECTED':'').'>'.$v.'</option>';
							}
							?>
						</select>
					</div>
					<div style="float:left;margin-left:10px;">
						<b>Then By:</b>
						<select name="sortfield2">
							<option value="">Select Field Name</option>
							<?php
							foreach($sortFields as $k => $v){
								echo '<option value="'.$k.'" '.($k==$sortField2?'SELECTED':'').'>'.$v.'</option>';
							}
							?>
						</select>
					</div>
					<div style="float:left;margin-left:10px;">
						<b>Order:</b>
						<select name="sortorder">
							<option value="">Ascending</option>
							<option value="desc" <?php echo ($sortOrder=="desc"?'SELECTED':''); ?>>Descending</option>
						</select>
					</div>
					<div style="float:right;margin-right:10px;">
						<input name="searchvar" type="hidden" value="<?php echo $searchVar; ?>" />
						<input name="formsubmit" type="submit" value="Sort" />
					</div>
				</form>
			</fieldset>
		</div>
		<?php
		$searchVar .= '&sortfield1='.$sortField1.'&sortfield2='.$sortField2.'&sortorder='.$sortOrder;
		$collManager->addSort($sortField1, $sortOrder);
		if($sortField2) $collManager->addSort($sortField2, $sortOrder);
		$recArr = $collManager->getSpecimenMap((($page-1)*$tableCount), $tableCount);

		$targetClid = $collManager->getSearchTerm("targetclid");

		$qryCnt = $collManager->getRecordCnt();
		$navStr = '<div style="float:right;">';
		if($page > 1){
			$navStr .= '<a href="listtabledisplay.php?'.$searchVar.'&page='.($page-1).'" title="Previous '.$tableCount.' records">&lt;&lt;</a>';
		}
		$navStr .= ' | ';
		$navStr .= ($page==1?1:(($page-1)*$tableCount)).'-'.($qryCnt<$tableCount*$page?$qryCnt:$tableCount*$page).' of '.$qryCnt.' records';
		$navStr .= ' | ';
		if($qryCnt > ($page*$tableCount)){
			$navStr .= '<a href="listtabledisplay.php?'.$searchVar.'&page='.($page+1).'" title="Next '.$tableCount.' records">&gt;&gt;</a>';
		}
		$navStr .= '</div>';
		?>
		<div style="width:850px;clear:both;">
			<div style="float:right">
				<?php
				echo $navStr;
				?>
			</div>
			<div class="navpath">
				<a href="../index.php">Home</a> &gt;&gt;
				<a href="index.php">Collections</a> &gt;&gt;
				<a href="harvestparams.php">Search Criteria</a> &gt;&gt;
				<b>Specimen Records Table</b>
			</div>
		</div>
		<form name="occurListForm" method="post" action="datasets/index.php" onsubmit="return validateOccurListForm(this)" target="_blank">
			<?php include('datasetinclude.php'); ?>
			<div id="tablediv">
				<?php
				if($recArr){
					?>
					<div style="clear:both;height:5px;"></div>
					<table class="styledtable" style="font-family:Arial;font-size:12px;">
						<tr>
							<th>Symbiota ID</th>
							<th>Collection</th>
							<th>Catalog Number</th>
							<th>Family</th>
							<th>Scientific Name</th>
							<th>Collector</th>
							<th>Number</th>
							<th>Event Date</th>
							<th>Country</th>
							<th>State/Province</th>
							<th>County</th>
							<th>Locality</th>
							<th>Decimal Lat.</th>
							<th>Decimal Long.</th>
							<th>Habitat</th>
							<th>Substrate</th>
							<th>Elevation</th>
						</tr>
						<?php
						$recCnt = 0;
						foreach($recArr as $occid => $occArr){
							$isEditor = false;
							if($SYMB_UID && ($IS_ADMIN
									|| (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($occArr['collid'],$USER_RIGHTS['CollAdmin']))
									|| (array_key_exists('CollEditor',$USER_RIGHTS) && in_array($occArr['collid'],$USER_RIGHTS['CollEditor'])))){
								$isEditor = true;
							}
							$collection = $occArr['instcode'];
							if($occArr['collcode']) $collection .= ':'.$occArr['collcode'];
							if($occArr['sciname']) $occArr['sciname'] = '<i>'.$occArr['sciname'].'</i> ';
							?>
							<tr <?php echo ($recCnt%2?'class="alt"':''); ?>>
								<td>
									<div class="datasetDiv" style="float:left;display:none"><input name="occid[]" type="checkbox" value="<?php echo $occid; ?>" /></div>
									<?php
									echo '<a href="#" onclick="return openIndPU('.$occid.",".($targetClid?$targetClid:"0").');">'.$occid.'</a> ';
									if($isEditor || ($SYMB_UID && $SYMB_UID == $occArr['obsuid'])){
										echo '<a href="editor/occurrenceeditor.php?occid='.$occid.'" target="_blank">';
										echo '<img src="../images/edit.png" style="height:13px;" title="Edit Record" />';
										echo '</a>';
									}
									if(isset($occArr['img'])){
										echo '<img src="../images/image.png" style="height:13px;margin-left:5px;" title="Has Image" />';
									}
									?>
								</td>
								<td><?php echo $collection; ?></td>
								<td><?php echo $occArr['catnum']; ?></td>
								<td><?php echo $occArr['family']; ?></td>
								<td><?php echo $occArr['sciname'].($occArr['author']?" ".$occArr['author']:""); ?></td>
								<td><?php echo $occArr['collector']; ?></td>
								<td><?php echo (array_key_exists("collnum",$occArr)?$occArr['collnum']:""); ?></td>
								<td><?php echo (array_key_exists("date",$occArr)?$occArr['date']:""); ?></td>
								<td><?php echo $occArr['country']; ?></td>
								<td><?php echo $occArr['state']; ?></td>
								<td><?php echo $occArr['county']; ?></td>
								<td>
								<?php
								$locStr = preg_replace('/<div.*?>.*?<\/div>/', '', $occArr['locality']);
								if(strlen($locStr)>80) $locStr = substr($locStr,0,80).'...';
								echo $locStr;
								?></td>
								<td><?php if($occArr['declat']) echo $occArr['declat']; ?></td>
								<td><?php if($occArr['declong']) echo $occArr['declong']; ?></td>
								<td><?php if(isset($occArr['habitat'])) echo ((strlen($occArr['habitat'])>80)?substr($occArr['habitat'],0,80).'...':$occArr['habitat']); ?></td>
								<td><?php if(isset($occArr['substrate'])) echo ((strlen($occArr['substrate'])>80)?substr($occArr['substrate'],0,80).'...':$occArr['substrate']); ?></td>
								<td><?php echo (array_key_exists("elev",$occArr)?$occArr['elev']:""); ?></td>
							</tr>
							<?php
							$recCnt++;
						}
						?>
					</table>
					<div style="clear:both;height:5px;"></div>
					<div style="width:790px;"><?php echo $navStr; ?></div>
					*Click on the Symbiota identifier in the first column to see Full Record Details.';
					<?php
				}
				else{
					echo '<div style="font-weight:bold;font-size:120%;">No records found matching the query</div>';
				}
				?>
			</div>
		</form>
	</div>
</body>
</html>