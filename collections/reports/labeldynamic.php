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
$action = array_key_exists('submitaction',$_POST)?$_POST['submitaction']:'';

//Sanitation
$hPrefix = filter_var($hPrefix, FILTER_SANITIZE_STRING);
$hMid = filter_var($hMid, FILTER_SANITIZE_STRING);
$hSuffix = filter_var($hSuffix, FILTER_SANITIZE_STRING);
$lFooter = filter_var($lFooter, FILTER_SANITIZE_STRING);
if(!is_numeric($labelFormatIndex)) $labelFormatIndex = '';
if(!is_numeric($labelFormat) && $labelFormat != 'packet') $labelFormat = 3;
if(!is_numeric($showcatalognumbers)) $showcatalognumbers = 0;
if(!is_numeric($useBarcode)) $useBarcode = 0;
if(!is_numeric($useSymbBarcode)) $useSymbBarcode = 0;
if(!is_numeric($barcodeOnly)) $barcodeOnly = 0;

$labelManager = new OccurrenceLabel();
$labelManager->setCollid($collid);

if($outputType == 'word'){
	header("Content-Type: application/vnd.ms-word; charset=".$CHARSET);
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("content-disposition: attachment;filename=labels.doc");
}
elseif($action == 'Export to CSV'){
	$labelManager->exportLabelCsvFile($_POST);
	exit;
}
else{
	header("Content-Type: text/html; charset=".$CHARSET);
}

$targetLabelFormatArr = $labelManager->getLabelFormatArr($labelFormatIndex);
if(!$targetLabelFormatArr){
	$labelFormatJson =
		'{"labelFormats": [
			{
				"name":"Default Herbarium Label",
				"displaySpeciesAuthor":1,
				"displayCatNum":0,
				"displayBarcode":0,
				"labelFormat":"1",
				"defaultStyles":"font-style:time roman;font-size:10pt",
				"defaultCss":"",
				"labelHeader":{
					"hPrefix":"Flora of ",
					"hMidCol":3,
					"hSuffix":" county",
					"style":"text-align:center;margin-bottom:10px;font:bold 14pt arial,sans-serif;clear:both;"
				},
				"labelFooter":{
					"textValue":"",
					"style":"text-align:center;margin-top:10px;font:bold 10pt arial,sans-serif;clear:both;"
				},
				"labelBlocks":[
					{"divElem":{"className":"labelBlockDiv","blocks":[
						{"divElem":{"className":"taxonomyDiv","style":"margin-top:5px;font-size:11pt;","blocks":[
							{"fields":[
								{"field":"identificationqualifier"},
								{"field":"speciesname","style":"font-weight:bold;font-style:italic"},
								{"field":"parentauthor"},
								{"field":"taxonrank","style":"font-weight:bold"},
								{"field":"infraspecificepithet","style":"font-weight:bold;font-style:italic"},
								{"field":"scientificnameauthorship"}
								],"delimiter":" "
							},
							{"fields":[{"field":"family","styles":["float:right"]}]}
						]}},
						{"fields":[{"field":"identifiedby","prefix":"Det by: "},{"field":"dateidentified"}]},
						{"fields":[{"field":"identificationreferences"}]},
						{"fields":[{"field":"identificationremarks"}]},
						{"fields":[{"field":"taxonremarks"}]},
						{"divElem":{"className":"localDiv","style":"margin-top:10px;font-size:11pt","blocks":[
							{"fields":[{"field":"country","style":"font-weight:bold"},{"field":"stateprovince","style":"font-weight:bold"},{"field":"county"},{"field":"municipality"},{"field":"locality"}],"delimiter":", "}
						]}},
						{"fields":[{"field":"verbatimcoordinates"}]},
						{"fields":[{"field":"decimallatitude"},{"field":"decimallongitude","style":"margin-left:10px"},{"field":"coordinateuncertaintyinmeters","prefix":"+-","suffix":" meters","style":"margin-left:10px"},{"field":"geodeticdatum","prefix":"[","suffix":"]","style":"margin-left:10px"}]},
						{"fields":[{"field":"elevationinmeters","prefix":"Elev: ","suffix":"m. "},{"field":"verbatimelevation"}]},
						{"fields":[{"field":"habitat","suffix":"."}]},
						{"fields":[{"field":"substrate","suffix":"."}]},
						{"fields":[{"field":"verbatimattributes"},{"field":"establishmentmeans"}],"delimiter":"; "},
						{"fields":[{"field":"associatedtaxa","prefix":"Associated species: ","style":"font-style:italic"}]},
						{"fields":[{"field":"occurrenceremarks"}]},
						{"fields":[{"field":"typestatus"}]},
						{"divElem":{"className":"collectorDiv","style":"margin-top:10px;","blocks":[
							{"fields":[{"field":"recordedby","style":"float:left"},{"field":"recordnumber","style":"float:left;margin-left:10px"},{"field":"eventdate","style":"float:right"}]},
							{"fields":[{"field":"associatedcollectors","prefix":"with: "}],"style":"clear:both; margin-left:10px;"}
						]}}
					]}}
				]
			},
			{
				"name":"Bird Dry Specimen",
				"displaySpeciesAuthor":0,
				"displayCatNum":1,
				"displayBarcode":0,
				"labelFormat":"2",
				"defaultStyles":"font-style:time roman;font-size:8pt",
				"defaultCss":"",
				"labelHeader":{
					"hPrefix":"Arizona State University Ornithology Collection",
					"hMidCol":0,
					"hSuffix":"",
					"style":"margin-bottom:5px;font:bold 7pt arial,sans-serif;clear:both;"
				},
				"labelFooter":{
					"textValue":"",
					"style":"text-align:center;margin-top:10px;font:bold 10pt arial,sans-serif;clear:both;"
				},
				"labelBlocks":[
					{"divElem":{"className":"labelBlockDiv","blocks":[
						{"fields":[{"field":"family","styles":["margin-bottom:2px;font-size:pt"]}]},
						{"divElem":{"className":"taxonomyDiv","style":"font-size:10pt;","blocks":[
							{"fields":[
								{"field":"identificationqualifier"},
								{"field":"speciesname","style":"font-weight:bold;font-style:italic"},
								{"field":"parentauthor"},
								{"field":"taxonrank","style":"font-weight:bold"},
								{"field":"infraspecificepithet","style":"font-weight:bold;font-style:italic"},
								{"field":"scientificnameauthorship"}
								],"delimiter":" "
							}
						]}},
						{"fields":[{"field":"identifiedby","prefix":"Det by: "},{"field":"dateidentified"}]},
						{"fields":[{"field":"identificationreferences"}]},
						{"fields":[{"field":"identificationremarks"}]},
						{"fields":[{"field":"taxonremarks"}]},
						{"fields":[{"field":"catalognumber","style":"font-weight:bold;font-size:14pt;margin:5pt 0pt;"}]},
						{"divElem":{"className":"localDiv","style":"margin-top:3px;padding-top:3px;border-top:3px solid black","blocks":[
							{"fields":[{"field":"country"},{"field":"stateprovince","prefix":", "},{"field":"county","prefix":", "},{"field":"municipality","prefix":", "},{"field":"locality","prefix":": "},{"field":"decimallatitude","prefix":": ","suffix":"° N"},{"field":"decimallongitude","prefix":" ","suffix":"° W"},{"field":"coordinateuncertaintyinmeters","prefix":" +-","suffix":" meters","style":"margin-left:10px"},{"field":"elevationinmeters","prefix":", ","suffix":"m."}]}
						]}},
						{"divElem":{"className":"collectorDiv","style":"margin-top:10px;font-size:6pt;clear:both;","blocks":[
							{"fields":[{"field":"recordedby","style":"float:left;","prefix":"Coll.: "},{"field":"preparations","style":"float:right","prefix":"Prep.: "}]}
						]}},
						{"divElem":{"className":"collectorDiv","style":"margin-top:10px;font-size:6pt;clear:both;","blocks":[
							{"fields":[{"field":"recordnumber","style":"float:left;","prefix":"Coll. No: "},{"field":"eventdate","style":"float:right","prefix":"Date: "}]}
						]}}
					]}}
				]
			}
		]}';
	$labelFormatArr = json_decode($labelFormatJson,true);
	$targetLabelFormatArr = $labelFormatArr['labelFormats'][0];
}

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
			if(isset($targetLabelFormatArr['defaultStyles'])) echo 'body{ '.$targetLabelFormatArr['defaultStyles']." } \n";
			?>
			.labelDiv { float:left; page-break-before:auto; page-break-inside:avoid; }
			<?php
			if($columnCount != 1){
				?>
				.labelDiv { width:<?php echo (floor(100/$columnCount)-3);?>%;padding:10px; }
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
			if($targetLabelFormatArr && $isEditor){
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
								echo '<div class="labelHeader" '.(isset($targetLabelFormatArr['labelHeader']['style'])?'style="'.$targetLabelFormatArr['labelHeader']['style'].'"':'').'>'.$headerStr.'</div>';
								//Output field data
								echo $labelManager->getLabelBlock($targetLabelFormatArr['labelBlocks'],$occArr);
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
								if($lFooter) echo '<div class="labelFooter" '.(isset($targetLabelFormatArr['labelFooter']['style'])?'style="'.$targetLabelFormatArr['labelFooter']['style'].'"':'').'>'.$lFooter.'</div>';
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
