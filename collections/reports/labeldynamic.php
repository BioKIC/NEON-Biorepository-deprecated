<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLabel.php');

$collid = $_POST['collid'];
$hPrefix = $_POST['lhprefix'];
$hMid = $_POST['lhmid'];
$hSuffix = $_POST['lhsuffix'];
$lFooter = $_POST['lfooter'];
$labelFormat = $_POST['labelformat'];
$labelFormatIndex = $_POST['labelformatindex'];
$showcatalognumbers = ((array_key_exists('catalognumbers',$_POST) && $_POST['catalognumbers'])?1:0);
$useBarcode = array_key_exists('bc',$_POST)?$_POST['bc']:0;
$useSymbBarcode = array_key_exists('symbbc',$_POST)?$_POST['symbbc']:0;
$barcodeOnly = array_key_exists('bconly',$_POST)?$_POST['bconly']:0;
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
if(!is_numeric($labelFormatIndex)) $labelFormatIndex = 0;
if(!is_numeric($labelFormat) && $labelFormat != 'packet') $labelFormat = 3;
if(!is_numeric($showcatalognumbers)) $showcatalognumbers = 0;
if(!is_numeric($useBarcode)) $useBarcode = 0;
if(!is_numeric($useSymbBarcode)) $useSymbBarcode = 0;
if(!is_numeric($barcodeOnly)) $barcodeOnly = 0;

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

$labelFormatIndex = 0;
$labelFormatJson =
'{"labelFormats": [
	{
		"name":"simple label",
		"displaySpeciesAuthor":1,
		"displayCatNum":1,
		"displayBarcode":1,
		"labelFormat":"2",
		"defaultStyles":"font-style:time roman;font-size:12px",
		"defaultCss":"",
		"labelHeader":{
			"hPrefix":"<i>Carex</i> of",
			"hMidCol":3,
			"hSuffix":"county",
			"style":"text-align:center;font-weight:bold;font-size:120%;clear:both;"
		},
		"labelFooter":{
			"textValue":"Arizona State University",
			"style":"text-align:center;font-weight:bold;font-size:120%;clear:both;"
		},
		"blocks":[
			{"fields":[{"field":"family","styles":["float:right"]}]},
			{"divClass":"taxonomyDiv","divStyle":"margin-top:5px","fields":[
				{"field":"identificationqualifier"},
				{"field":"speciesname","fieldStyle":"font-weight:bold;font-style:italic"},
				{"field":"parentauthor"},
				{"field":"taxonrank","fieldStyle":"font-weight:bold"},
				{"field":"infraspecificepithet","fieldStyle":"font-weight:bold;font-style:italic"},
				{"field":"scientificnameauthorship"}
			],"delimiter":" "},
			{"fields":[{"field":"identifiedby","prefix":"Det by: "},{"field":"dateidentified"}]},
			{"fields":[{"field":"identificationreferences"}]},
			{"fields":[{"field":"identificationremarks"}]},
			{"fields":[{"field":"taxonremarks"}]},
			{"divClass":"localDiv","divStyle":"margin-top:10px;","fields":[
				{"field":"country"},
				{"field":"stateprovince"},
				{"field":"county"},
				{"field":"municipality"},
				{"field":"locality"}
			],"delimiter":", "},
			{"fields":[{"field":"verbatimcoordinates"}]},
			{"fields":[{"field":"decimallatitude"},{"field":"decimallongitude","fieldStyle":"margin-left:10px"},{"field":"coordinateuncertaintyinmeters","prefix":"+-","suffix":" meters","fieldStyle":"margin-left:10px"},{"field":"geodeticdatum","prefix":"[","suffix":"]","fieldStyle":"margin-left:10px"}]},
			{"fields":[{"field":"elevationinmeters","prefix":"Elev: ","suffix":"m. "},{"field":"verbatimelevation"}]},
			{"fields":[{"field":"habitat","suffix":"."}]},
			{"fields":[{"field":"substrate","suffix":"."}]},
			{"fields":[{"field":"verbatimattributes"},{"field":"establishmentmeans"}],"delimiter":"; "},
			{"fields":[{"field":"associatedtaxa","prefix":"Associated species: ","fieldStyle":"font-style:italic"}]},
			{"fields":[{"field":"occurrenceremarks"}]},
			{"fields":[{"field":"typestatus"}]},
			{"divClass":"collectorDiv","fields":[{"field":"recordedby","fieldStyle":"float:left"},{"field":"recordnumber","fieldStyle":"float:left;margin-left:10px"},{"field":"eventdate","fieldStyle":"float:right"}]},
			{"fields":[{"field":"associatedcollectors","prefix":"With: ","fieldStyle":"clear:both; margin-left:10px;"}]}
		]
	}
]}';
$labelFormatArr = json_decode($labelFormatJson,true);
$targetLabelFormat = $labelFormatArr['labelFormats'][$labelFormatIndex];

//$targetLabelFormat = $labelManager->getLabelFormatArr($labelFormatIndex);

$columnCount = 1;
if(is_numeric($labelFormat)) $columnCount = $labelFormat;

$isEditor = 0;
if($SYMB_UID){
	if($IS_ADMIN) $isEditor = 1;
	elseif(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($labelManager->getCollid(),$USER_RIGHTS["CollAdmin"])) $isEditor = 1;
	elseif(array_key_exists("CollEditor",$USER_RIGHTS) && in_array($labelManager->getCollid(),$USER_RIGHTS["CollEditor"])) $isEditor = 1;
}
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Labels</title>
		<style type="text/css">
			<?php
			if(isset($targetLabelFormat['defaultStyles'])) echo 'body:{ '.$targetLabelFormat['defaultStyles']." } \n";
			?>
			.labelDiv { float:left; page-break-before:auto; page-break-inside:avoid; }
			<?php
			if($columnCount != 1){
				?>
				.labelDiv { width:<?php echo (1000/$columnCount); ?>;font-size:10pt;padding:10px 23px 10px 23px; }
				<?php
			}
			if($labelFormat == 'packet'){
				?>
				.foldMarks1 { clear:both;padding-top:285px; }
				.foldMarks1 span { margin-left:77px; margin-right:80px; }
				.foldMarks2 { clear:both;padding-top:355px;padding-bottom:10px; }
				.foldMarks2 span { margin-left:77px; margin-right:80px; }
				.labelDiv {
					clear:both;
					margin-top: 10px;
					margin-left: auto;
					margin-right: auto;
					width: 500px;
					page-break-before:auto;
					page-break-inside:avoid;
				}
				.labelDiv {
					width:500px;
					margin:50px;
					padding:10px 50px;
					font-size: 80%;
				}
				.family { display:none }
				<?php
			}
			?>
			.cnBarcodeDiv { clear:both; padding-top:15px; }
			.catalogNumber { clear:both; text-align:center; }
			.otherCatalogNumbers { clear:both; text-align:center; }
			.symbBarcode { padding-top:10px; }
		</style>
	</head>
	<body style="background-color:#ffffff;">
		<div class="bodyDiv">
			<?php
			if($labelFormatArr && $isEditor){
				$includeSpeciesAuthor = ((array_key_exists('speciesauthors',$_POST) && $_POST['speciesauthors'])?1:0);
				$labelArr = $labelManager->getLabelArray($_POST['occid'], $includeSpeciesAuthor);
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
						//Build label header string
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
							if($labelCnt%$columnCount == 1){
								if($labelCnt > 1) echo '</div>';
								echo '<div class="pageDiv">';
								$rowCnt++;
							}
							?>
							<div class="labelDiv">
								<?php
								echo '<div class="labelHeader" '.(isset($targetLabelFormat['labelHeader']['style'])?'style="'.$targetLabelFormat['labelHeader']['style'].'"':'').'>'.$headerStr.'</div>';
								//Output field data
								foreach($targetLabelFormat['blocks'] as $blockArr){
									$delimiter = (isset($blockArr['delimiter'])?$blockArr['delimiter']:'');
									$cnt = 0;
									$outputStr = '';
									foreach($blockArr['fields'] as $fieldArr){
										$fieldName = $fieldArr['field'];
										$fieldValue = trim($occArr[$fieldName]);
										if($fieldValue){
											if($delimiter && $cnt) $outputStr .= $delimiter;
											if(isset($fieldArr['prefix']) && $fieldArr['prefix']){
												$outputStr .= '<span class="'.$fieldName.'Prefix" '.(isset($fieldArr['prefixStyle'])?'style="'.$fieldArr['prefixStyle'].'"':'').'>'.$fieldArr['prefix'].'</span>';
											}
											$outputStr .= '<span class="'.$fieldName.'" '.(isset($fieldArr['fieldStyle'])?'style="'.$fieldArr['fieldStyle'].'"':'').'>'.$fieldValue.'</span>';
											if(isset($fieldArr['suffix']) && $fieldArr['suffix']){
												$outputStr .= '<span class="'.$fieldName.'Suffix" '.(isset($fieldArr['suffixStyle'])?'style="'.$fieldArr['sufffixStyle'].'"':'').'>'.$fieldArr['suffix'].'</span>';
											}
										}
										$cnt++;
									}
									if($outputStr){
										echo '<div '.(isset($blockArr['divClass'])?'class="'.$blockArr['divClass'].'"':'').' '.(isset($blockArr['divStyle'])?'style="'.$blockArr['divStyle'].'"':'').'>';
										echo $outputStr;
										echo '</div>'."\n";
									}
								}
								if($useBarcode && $occArr['catalognumber']){
									?>
									<div class="cnBarcodeDiv">
										<img src="getBarcode.php?bcheight=40&bctext=<?php echo $occArr['catalognumber']; ?>" />
									</div>
									<?php
									if($occArr['othercatalognumbers']){
										?>
										<div class="otherCatalogNumbers">
											<?php echo $occArr['othercatalognumbers']; ?>
										</div>
										<?php
									}
								}
								elseif($showcatalognumbers){
									if($occArr['catalognumber']){
										?>
										<div class="catalogNumber">
											<?php echo $occArr['catalognumber']; ?>
										</div>
										<?php
									}
									if($occArr['othercatalognumbers']){
										?>
										<div class="otherCatalogNumbers">
											<?php echo $occArr['othercatalognumbers']; ?>
										</div>
										<?php
									}
								}
								if($lFooter) echo '<div class="labelFooter" '.(isset($targetLabelFormat['labelHeader']['style'])?'style="'.$targetLabelFormat['labelHeader']['style'].'"':'').'>'.$lFooter.'</div>';
								if($useSymbBarcode){
									?>
									<hr style="border:dashed;" />
									<div class="symbBarcode">
										<img src="getBarcode.php?bcheight=40&bctext=<?php echo $occid; ?>" />
									</div>
									<?php
									if($occArr['catalognumber']){
										?>
										<div class="catalogNumber">
											<?php echo $occArr['catalognumber']; ?>
										</div>
										<?php
									}
								}
								?>
							</div>
							<?php
						}
					}
				}
				echo '</div>';		//Closing pageDiv
				if(!$labelCnt) echo '<div style="font-weight:bold;text-size: 120%">No records were retrieved. Perhaps the quantity values were all set to 0?</div>';
			}
			else{
				echo '<div style="font-weight:bold;text-size: 120%">';
				if($labelFormatArr) echo 'ERROR: Unable to parse JSON that defines the label format profile ';
				else 'ERROR: Permissions issue';
				echo '</div>';
			}
			?>
		</div>
	</body>
</html>
