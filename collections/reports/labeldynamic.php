<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLabel.php');

$collid = $_POST['collid'];
$hPrefix = $_POST['lhprefix'];
$hMid = $_POST['lhmid'];
$hSuffix = $_POST['lhsuffix'];
$lFooter = $_POST['lfooter'];
$labelFormat = $_POST['labelformat'];
$rowPerPage = $_POST['rowperpage'];
$showcatalognumbers = ((array_key_exists('catalognumbers',$_POST) && $_POST['catalognumbers'])?1:0);
$useBarcode = array_key_exists('bc',$_POST)?$_POST['bc']:0;
$useSymbBarcode = array_key_exists('symbbc',$_POST)?$_POST['symbbc']:0;
$barcodeOnly = array_key_exists('bconly',$_POST)?$_POST['bconly']:0;
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';
$outputType = array_key_exists('outputtype',$_POST)?$_POST['outputtype']:'html';

if($outputType == 'word'){
	header("Content-Type: application/vnd.ms-word; charset=".$CHARSET);
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("content-disposition: attachment;filename=labels.doc");
}
else{
	header("Content-Type: text/html; charset=".$CHARSET);
}

//Sanitation
$hPrefix = filter_var($hPrefix, FILTER_SANITIZE_STRING);
$hMid = filter_var($hMid, FILTER_SANITIZE_STRING);
$hSuffix = filter_var($hSuffix, FILTER_SANITIZE_STRING);
$lFooter = filter_var($lFooter, FILTER_SANITIZE_STRING);
if(!is_numeric($labelFormat) && $labelFormat != 'packet') $labelFormat = 3;
if(!is_numeric($rowPerPage)) $rowPerPage = 2;
if(!is_numeric($showcatalognumbers)) $showcatalognumbers = 0;
if(!is_numeric($useBarcode)) $useBarcode = 0;
if(!is_numeric($useSymbBarcode)) $useSymbBarcode = 0;
if(!is_numeric($barcodeOnly)) $barcodeOnly = 0;
$action = filter_var($action, FILTER_SANITIZE_STRING);

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

/*
 * Example of a label profile definition
 *
 * {"labelFormats": [
	{"labelFormat":[
		{"name":"simple label","labelFormat":"2","rowsPerPage":"2","displayCatNum":0,"displayBarcode":0,"style":[{"font-style":"time roman","font-size":"12px"}],"lines":[
			{"labelHeader":[
				{"style":[{"text-align":"center","font-weight":"bold","font-size":"120%"}]},
				{"hPrefix":"<i>Carex</i> of"},
				{"hMidCol":"county"},
				{"hSuffix":"county"}
			]},
			{"1":[
				{"col":"sciname","style":[{"float":"left","font-weight":"bold"}]},
				{"col":"family","style":[{"float":"right"}]}
			]},
			{"2":[
				{"col":"sciname","style":[{"float":"left","font-weight":"bold"}]},
				{"col":"family","style":[{"float":"right"}]}
			]},
			{"labelFooter":[
				{"style":[{"text-align":"center","font-weight":"bold","font-size":"120%"}]},
				{"textValue":"Arizona State University"}
			]},
		]}
	]}
]}
 */

$columnCount = 1;
if(is_numeric($labelFormat)) $columnCount = $labelFormat;

$isEditor = 0;
if($SYMB_UID){
	if($IS_ADMIN) $isEditor = 1;
	elseif(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($labelManager->getCollid(),$USER_RIGHTS["CollAdmin"])) $isEditor = 1;
	elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($labelManager->getCollid(),$USER_RIGHTS["CollEditor"])) $isEditor = 1;
}
if($action == 'Export to CSV'){
	$labelManager->exportLabelCsvFile($_POST);
}
else{
	?>
	<html>
		<head>
			<title><?php echo $DEFAULT_TITLE; ?> Labels</title>
			<style type="text/css">
				<?php
				if($columnCount != 1){
					?>
					table.labels td { width:<?php echo (1000/$columnCount); ?>;font-size:10pt; }
					table.labels td:first-child {padding:10px 23px 10px 0px;}
					table.labels td:not(:first-child):not(:last-child) {padding:10px 23px 10px 23px;}
					table.labels td:last-child {padding:10px 0px 10px 23px;}
					<?php
				}
				if($labelFormat == 'packet'){
					?>
					.foldMarks1 { clear:both;padding-top:285px; }
					.foldMarks1 span { margin-left:77px; margin-right:80px; }
					.foldMarks2 { clear:both;padding-top:355px;padding-bottom:10px; }
					.foldMarks2 span { margin-left:77px; margin-right:80px; }
					table.labels {
						clear:both;
						margin-top: 10px;
						margin-left: auto;
						margin-right: auto;
						width: 500px;
						page-break-before:auto;
						page-break-inside:avoid;
					}
					table.labels td {
						width:500px;
						margin:50px;
						padding:10px 50px;
						font-size: 80%;
					}
					.family { display:none }

					<?php
				}
				?>
			</style>
		</head>
		<body style="background-color:#ffffff;">
			<div>
				<?php
				if($action && $isEditor){
					$includeSpeciesAuthor = ((array_key_exists('speciesauthors',$_POST) && $_POST['speciesauthors'])?1:0);
					$labelArr = $labelManager->getLabelArray($_POST['occid'], $includeSpeciesAuthor);
					$totalLabelCnt = count($labelArr);
					$labelCnt = 0;
					$rowCnt = 0;
					foreach($labelArr as $occid => $occArr){
						if($barcodeOnly){
							if($occArr['catalognumber']){
								?>
								<div class="barcodeonly">
									<img src="getBarcode.php?bcheight=40&bctext=<?php echo $occArr['catalognumber']; ?>" />
								</div>
								<?php
								$labelCnt++;
							}
						}
						else{
							$midStr = '';
							if($hMid == 1) $midStr = $occArr['country'];
							elseif($hMid == 2) $midStr = $occArr['stateprovince'];
							elseif($hMid == 3) $midStr = $occArr['county'];
							elseif($hMid == 4) $midStr = $occArr['family'];
							$headerStr = '';
							if($hPrefix || $midStr || $hSuffix){
								$headerStrArr = array();
								$headerStrArr[] = trim($hPrefix);
								$headerStrArr[] = trim($midStr);
								$headerStrArr[] = trim($hSuffix);
								$headerStr = implode(" ",$headerStrArr);
							}

							$dupCnt = $_POST['q-'.$occid];
							for($i = 0;$i < $dupCnt;$i++){
								$labelCnt++;
								if($labelFormat == 'packet'){
									echo '<div class="foldMarks1"><span style="float:left;">+</span><span style="float:right;">+</span></div>';
									echo '<div class="foldMarks2"><span style="float:left;">+</span><span style="float:right;">+</span></div>';
								}
								if($columnCount == 1 || $labelCnt%$columnCount == 1){
									?>
									<table class="labels"><tr>
									<?php
									$rowCnt++;
								}
								?>
								<td valign="top">
									<?php
									if($headerStr){
										?>
										<div class="lheader">
											<?php echo $headerStr; ?>
										</div>
										<?php
									}
									if($hMid != 4) echo '<div class="family">'.$occArr['family'].'</div>'; ?>
									<div class="scientificnamediv">
										<?php
										if($occArr['identificationqualifier']) echo '<span class="identificationqualifier">'.$occArr['identificationqualifier'].'</span> ';
										$scinameStr = $occArr['scientificname'];
										$parentAuthor = (array_key_exists('parentauthor',$occArr)?' '.$occArr['parentauthor']:'');
										$scinameStr = str_replace(' sp. ','</i></b>'.$parentAuthor.' <b>sp.</b>',$scinameStr);
										$scinameStr = str_replace(' subsp. ','</i></b>'.$parentAuthor.' <b>subsp. <i>',$scinameStr);
										$scinameStr = str_replace(' ssp. ','</i></b>'.$parentAuthor.' <b>ssp. <i>',$scinameStr);
										$scinameStr = str_replace(' var. ','</i></b>'.$parentAuthor.' <b>var. <i>',$scinameStr);
										$scinameStr = str_replace(' variety ','</i></b>'.$parentAuthor.' <b>var. <i>',$scinameStr);
										$scinameStr = str_replace(' Variety ','</i></b>'.$parentAuthor.' <b>var. <i>',$scinameStr);
										$scinameStr = str_replace(' v. ','</i></b>'.$parentAuthor.' <b>var. <i>',$scinameStr);
										$scinameStr = str_replace(' f. ','</i></b>'.$parentAuthor.' <b>f. <i>',$scinameStr);
										$scinameStr = str_replace(' cf. ','</i></b>'.$parentAuthor.' <b>cf. <i>',$scinameStr);
										$scinameStr = str_replace(' aff. ','</i></b>'.$parentAuthor.' <b>aff. <i>',$scinameStr);
										?>
										<span class="sciname">
											<b><i><?php echo $scinameStr; ?></i></b>
										</span>
										<span class="scientificnameauthorship"><?php echo $occArr['scientificnameauthorship']; ?></span>
									</div>
									<?php
									if($occArr['identifiedby']){
										?>
										<div class="identifiedbydiv">
											Det by:
											<span class="identifiedby"><?php echo $occArr['identifiedby']; ?></span>
											<span class="dateidentified"><?php echo $occArr['dateidentified']; ?></span>
										</div>
										<?php
										if($occArr['identificationreferences'] || $occArr['identificationremarks'] || $occArr['taxonremarks']){
											?>
											<div class="identificationreferences">
												<?php echo $occArr['identificationreferences']; ?>
											</div>
											<div class="identificationremarks">
												<?php echo $occArr['identificationremarks']; ?>
											</div>
											<div class="taxonremarks">
												<?php echo $occArr['taxonremarks']; ?>
											</div>
											<?php
										}
									}
									?>
									<div class="loc1div" style="margin-top:10px;">
										<span class="country"><?php echo $occArr['country'].($occArr['country']?', ':''); ?></span>
										<span class="stateprovince"><?php echo $occArr['stateprovince'].($occArr['stateprovince']?', ':''); ?></span>
										<?php
										$countyStr = trim($occArr['county']);
										if($countyStr){
											//if(!stripos($occArr['county'],' County') && !stripos($occArr['county'],' Parish')) $countyStr .= ' County';
											$countyStr .= ', ';
										}
										?>
										<span class="county"><?php echo $countyStr; ?></span>
										<span class="municipality"><?php echo $occArr['municipality'].($occArr['municipality']?', ':''); ?></span>
										<span class="locality">
											<?php
											$locStr = trim($occArr['locality']);
											if(substr($locStr,-1) != '.'){
												$locStr .= '.';
											}
											echo $locStr;
											?>
										</span>
									</div>
									<?php
									if($occArr['decimallatitude'] || $occArr['verbatimcoordinates']){
										?>
										<div class="loc2div">
											<?php
											if($occArr['verbatimcoordinates']){
												?>
												<span class="verbatimcoordinates">
													<?php echo $occArr['verbatimcoordinates']; ?>
												</span>
												<?php
											}
											else{
												echo '<span class="decimallatitude">'.$occArr['decimallatitude'].'</span>'.($occArr['decimallatitude']>0?'N':'S');
												echo '<span class="decimallongitude" style="margin-left:10px;">'.$occArr['decimallongitude'].'</span>'.($occArr['decimallongitude']>0?'E':'W').' ';
											}
											if($occArr['coordinateuncertaintyinmeters']) echo '<span style="margin-left:10px;">+-'.$occArr['coordinateuncertaintyinmeters'].' meters</span>';
											if($occArr['geodeticdatum']) echo '<span style="margin-left:10px;">['.$occArr['geodeticdatum'].']</span>';
											?>
										</div>
										<?php
									}
									if($occArr['minimumelevationinmeters']){
										?>
										<div class="elevdiv">
											Elev:
											<?php
											echo '<span class="minimumelevationinmeters">'.$occArr['minimumelevationinmeters'].'</span>'.
											($occArr['maximumelevationinmeters']?' - <span class="maximumelevationinmeters">'.$occArr['maximumelevationinmeters'].'<span>':''),'m. ';
											if($occArr['verbatimelevation']) echo ' ('.$occArr['verbatimelevation'].')';
											?>
										</div>
										<?php
									}
									if($occArr['habitat']){
										?>
										<div class="habitat">
											<?php
											$habStr = trim($occArr['habitat']);
											if(substr($habStr,-1) != '.'){
												$habStr .= '.';
											}
											echo $habStr;
											?>
										</div>
										<?php
									}
									if($occArr['substrate']){
										?>
										<div class="substrate">
											<?php
											$substrateStr = trim($occArr['substrate']);
											if(substr($substrateStr,-1) != '.'){
												$substrateStr .= '.';
											}
											echo $substrateStr;
											?>
										</div>
										<?php
									}
									if($occArr['verbatimattributes'] || $occArr['establishmentmeans']){
										?>
										<div>
											<span class="verbatimattributes"><?php echo $occArr['verbatimattributes']; ?></span>
											<?php echo ($occArr['verbatimattributes'] && $occArr['establishmentmeans']?'; ':''); ?>
											<span class="establishmentmeans">
												<?php echo $occArr['establishmentmeans']; ?>
											</span>
										</div>
										<?php
									}
									if($occArr['associatedtaxa']){
										?>
										<div>
											Associated species:
											<span class="associatedtaxa"><?php echo $occArr['associatedtaxa']; ?></span>
										</div>
										<?php
									}
									if($occArr['occurrenceremarks']){
										?>
										<div class="occurrenceremarks"><?php echo $occArr['occurrenceremarks']; ?></div>
										<?php
									}
									if($occArr['typestatus']){
										?>
										<div class="typestatus"><?php echo $occArr['typestatus']; ?></div>
										<?php
									}
									?>
									<div class="collectordiv">
										<div class="collectordiv1" style="float:left;">
											<span class="recordedby"><?php echo $occArr['recordedby']; ?></span>
											<span class="recordnumber"><?php echo $occArr['recordnumber']; ?></span>
										</div>
										<div class="collectordiv2" style="float:right;">
											<span class="eventdate"><?php echo $occArr['eventdate']; ?></span>
										</div>
										<?php
										if($occArr['associatedcollectors']){
											?>
											<div class="associatedcollectors" style="clear:both;margin-left:10px;">
												With: <?php echo $occArr['associatedcollectors']; ?>
											</div>
											<?php
										}
										?>
									</div>
									<?php
									if($useBarcode && $occArr['catalognumber']){
										?>
										<div class="cnbarcode" style="clear:both;padding-top:15px;">
											<img src="getBarcode.php?bcheight=40&bctext=<?php echo $occArr['catalognumber']; ?>" />
										</div>
										<?php
										if($occArr['othercatalognumbers']){
											?>
											<div class="othercatalognumbers" style="clear:both;text-align:center;">
												<?php echo $occArr['othercatalognumbers']; ?>
											</div>
											<?php
										}
									}
									elseif($showcatalognumbers){
										if($occArr['catalognumber']){
											?>
											<div class="catalognumber" style="clear:both;text-align:center;">
												<?php echo $occArr['catalognumber']; ?>
											</div>
											<?php
										}
										if($occArr['othercatalognumbers']){
											?>
											<div class="othercatalognumbers" style="clear:both;text-align:center;">
												<?php echo $occArr['othercatalognumbers']; ?>
											</div>
											<?php
										}
									}
									?>
									<div class="lfooter"><?php echo $lFooter; ?></div>
									<?php
									if($useSymbBarcode){
										?>
										<hr style="border:dashed;" />
										<div class="symbbarcode" style="padding-top:10px;">
											<img src="getBarcode.php?bcheight=40&bctext=<?php echo $occid; ?>" />
										</div>
										<?php
										if($occArr['catalognumber']){
											?>
											<div class="catalognumber" style="clear:both;text-align:center;">
												<?php echo $occArr['catalognumber']; ?>
											</div>
											<?php
										}
									}
									?>
								</td>
								<?php
								if($labelCnt%$columnCount == 0 || $labelCnt == $totalLabelCnt){
									if($labelCnt == $totalLabelCnt){
										//Add missing <td> tags
										$remaining = $columnCount-($labelCnt%$columnCount);
										for($i = 0;$i < $remaining;$i++){
											echo '<td>$nbsp;</td>';
										}
									}
									?>
									</tr></table>
									<?php
									echo 'row: '.$rowCnt.' rowPerPage: '.$rowPerPage.' mod: '.$rowCnt%$rowPerPage;
									if($rowCnt%$rowPerPage === 0) echo '<p style="page-break-before: always" />';
								}
							}
						}
					}
					if(!$labelCnt) echo '<div style="font-weight:bold;text-size: 120%">No records were retrieved. Perhaps the quantity values were all set to 0?</div>';
				}
				?>
			</div>
		</body>
	</html>
	<?php
}
?>