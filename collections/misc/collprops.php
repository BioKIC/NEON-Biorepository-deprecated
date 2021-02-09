<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/collections/misc/collprops.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProfile.php');
header('Content-Type: text/html; charset='.$CHARSET);

$action = array_key_exists('action',$_POST)?$_POST['action']:'';
$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;

$collManager = new OccurrenceCollectionProfile();
$collManager->setCollid($collid);

$isEditor = 0;
if($SYMB_UID){
	if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collId,$USER_RIGHTS['CollAdmin']))){
		$isEditor = 1;
	}
}

if($isEditor){
	if($action == ''){

	}
}
$collMeta = $collManager->getCollectionMetadata();
?>
<html>
<head>
	<title><?php echo $collMeta['collectionname']; ?> Special Properties</title>
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
	<script>
		function verifyAddContactForm(f){
			if(f.uid.value == ""){
				alert("Please select a user from list");
				return false;
			}
			return true;
		}
	</script>
	<style type="text/css">
		fieldset{ padding: 10px }
		legend{ font-weight: bold }
		.fieldRowDiv{ clear:both; margin: 2px 0px; }
		.fieldDiv{ float:left; margin: 2px 10px 2px 0px; }
		.fieldLabel{ font-weight: bold; display: block; }
		.fieldDiv button{ margin-top: 10px; }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class='navpath'>
		<a href='../../index.php'>Home</a> &gt;&gt;
		<a href='collprofiles.php?emode=1&collid=<?php echo $collId; ?>'>Special Properties</a> &gt;&gt;
		<b><?php echo $collMeta['collectionname'].' Contacts'; ?></b>
	</div>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor){
			$dynamicProps = $collManager->getDynamicPropoerties();
			?>
			<fieldset style="margin:15px;padding:15px;">
				<legend>Publication Properties</legend>
				<form name="addContactForm" action="collcontact.php" method="post" onsubmit="return verifyAddContactForm(this)">
					<div class="fieldRowDiv">
						<div class="fieldDiv">
							<span class="fieldLabel">Title Override: </span>
							<input name="titleOverride" type="text" value="<?php echo (isset($dynamicProps['publicationProps']['titleOverride'])?:''); ?>" />
						</div>
						<div class="fieldDiv">
							<span class="fieldLabel">: </span>
							<input name="" type="text" value="" />
						</div>
					</div>
					<div style="margin:15px;">
						<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
						<input name="action" type="submit" value="Save Properties" />
					</div>
				</form>
			</fieldset>
			<fieldset style="margin:15px;padding:15px;">
				<legend>Occurrence Editor Properties</legend>
				<?php
				$moduleArr = array();
				if(isset($dynamicProps['editorProps']['modules-panel'])) $moduleArr = $dynamicProps['editorProps']['modules-panel'];
				?>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<span class="fieldLabel">Title Override: </span>
						<input name="paleomodule" type="checkbox" value="1" <?php echo (isset($moduleArr['paleo']['status']) && $moduleArr['paleo']['status']?'checked':''); ?> />
					</div>
				</div>
			</fieldset>
			<?php
		}
		else echo '<div style="font-weight:bold;font-size:120%;">Unauthorized to edit special collection properties</div>';
		?>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>