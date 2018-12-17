<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/ShipmentManager.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl='.$CLIENT_ROOT.'/neon/shipment/manifestloader.php');

$action = array_key_exists("action",$_REQUEST)?$_REQUEST["action"]:"";
$ulFileName = array_key_exists("ulfilename",$_REQUEST)?$_REQUEST["ulfilename"]:"";

$isEditor = false;
if($IS_ADMIN){
	$isEditor = true;
}

$loaderManager = new ShipmentManager();

$fieldMap = array();
if($isEditor){
	if($ulFileName){
		$loaderManager->setUploadFileName($ulFileName);
	}
	if(array_key_exists("sf",$_POST)){
		//Grab field mapping, if mapping form was submitted
		$targetFields = $_REQUEST["tf"];
 		$sourceFields = $_REQUEST["sf"];
		for($x = 0;$x<count($targetFields);$x++){
			if($targetFields[$x] && $sourceFields[$x]) $fieldMap[$sourceFields[$x]] = $targetFields[$x];
		}
		$loaderManager->setFieldMap($fieldMap);
	}
	if($action == 'downloadcsv'){
		$loaderManager->exportUploadTerms();
		exit;
	}
}
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Manifest Loader</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
	<link href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="../../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<script type="text/javascript">
	</script>
</head>
<body>
<?php
$displayLeftMenu = false;
include($SERVER_ROOT.'/header.php');
?>
<div class="navpath">
	<a href="../../index.php">Home</a> &gt;&gt;
	<a href="index.php"><b>NEON Biorepository Management Tools</b></a> &gt;&gt;
	<b>Manifest Loader</b>
</div>
<?php
if($isEditor){
	?>
	<div id="innertext">
		<h1>Manifest Loader</h1>
		<div style="margin:30px;">
			<?php
			if($action == 'Map Input File' || $action == 'Verify Mapping'){
				if(!$ulFileName) $loaderManager->uploadManifestFile();
				$loaderManager->analyzeUpload();
				?>
				<form name="mapform" action="manifestloader.php" method="post">
					<fieldset style="width:90%;">
						<legend style="font-weight:bold;font-size:120%;">Manifest Upload Form</legend>
						<div style="margin:10px;">
						</div>
						<table border="1" cellpadding="2" style="border:1px solid black">
							<tr>
								<th>
									Source Field
								</th>
								<th>
									Target Field
								</th>
							</tr>
							<?php
							$sourceArr = $loaderManager->getSourceArr();
							$targetArr = $loaderManager->getTargetArr();
							$translationMap = array();
							foreach($sourceArr as $sourceField){
								?>
								<tr>
									<td style='padding:2px;'>
										<?php echo $sourceField; ?>
										<input type="hidden" name="sf[]" value="<?php echo $sourceField; ?>" />
									</td>
									<td>
										<?php
										$translatedSourceField = $sourceField;
										if(array_key_exists($translatedSourceField, $translationMap)) $translatedSourceField = $translationMap[$translatedSourceField];
										$bgColor = 'yellow';
										if(array_key_exists($translatedSourceField,$fieldMap)) $bgColor = 'white';
										elseif(in_array($translatedSourceField, $targetArr)) $bgColor = 'white';
										?>
										<select name="tf[]" style="background:<?php echo $bgColor; ?>">
											<option value="">Field Unmapped</option>
											<option value="">-------------------------</option>
											<?php
											echo '<option value="unmapped">Leave Field Unmapped</option>';
											if(array_key_exists($translatedSourceField,$fieldMap)){
												foreach($targetArr as $targetField){
													echo '<option '.($fieldMap[$translatedSourceField]==$targetField?'SELECTED':'').'>'.$targetField.'</option>';
												}
											}
											else{
												foreach($targetArr as $targetField){
													echo '<option '.($translatedSourceField==$targetField?'SELECTED':'').'>'.$targetField.'</option>';
												}
											}
											?>
										</select>
									</td>
								</tr>
								<?php
							}
							?>
						</table>
						<div style="margin:10px;">
							<input type="submit" name="action" value="Verify Mapping" />
							<input type="submit" name="action" value="Process Manifest" />
							<input type="hidden" name="ulfilename" value="<?php echo $loaderManager->getUploadFileName(); ?>" />
						</div>
					</fieldset>
				</form>
				<?php
			}
			elseif($action == 'Process Manifest'){
				echo '<ul>';
				$loaderManager->setUploadFileName($ulFileName);
				$loaderManager->setFieldMap($fieldMap);
				$loaderManager->uploadData();
				echo '</ul>';
				?>
				<fieldset style="margin:15px;padding:15px">
					<legend><b>Navigation Menu</b></legend>
					<div style="margin:5px"><a href="samplecheckin.php">Sample Check-in</a></div>
					<div style="margin:5px"><a href="manifestviewer.php">Go to Manifest View</a></div>
				</fieldset>
				<?php
			}
			else{
				?>
				<div>
					<form name="uploadform" action="manifestloader.php" method="post" enctype="multipart/form-data" onsubmit="return verifyUploadForm(this)">
						<fieldset style="width:90%;">
							<legend style="font-weight:bold;font-size:120%;">Manifest Upload Form</legend>
							<div style="margin:10px;">
							</div>
							<input type='hidden' name='MAX_FILE_SIZE' value='10000000' />
							<div>
								<div>
									<b>Upload File:</b>
									<div style="margin:10px;">
										<input id="genuploadfile" name="uploadfile" type="file" size="40" />
									</div>
								</div>
								<div style="margin:10px;">
									<input type="submit" name="action" value="Map Input File" />
								</div>
							</div>
						</fieldset>
					</form>
				</div>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
else{
	?>
	<div style='font-weight:bold;margin:30px;'>
		You do not have permissions to upload and process NEON manifests
	</div>
	<?php
}
include($SERVER_ROOT.'/footer.php');
?>
</body>
</html>