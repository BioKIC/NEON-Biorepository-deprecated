<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSesar.php');
header("Content-Type: text/html; charset=".$CHARSET);
ini_set('max_execution_time', 3600);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/igsnmanagement.php?'.$_SERVER['QUERY_STRING']);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$username = array_key_exists('username',$_REQUEST)?$_REQUEST['username']:'';
$pwd = array_key_exists('pwd',$_REQUEST)?$_REQUEST['pwd']:'';
$namespace = array_key_exists('namespace',$_REQUEST)?$_REQUEST['namespace']:'';
$generationMethod = array_key_exists('generationMethod',$_REQUEST)?$_REQUEST['generationMethod']:'';
$processingCount = array_key_exists('processingCount',$_REQUEST)?$_REQUEST['processingCount']:10;
$action = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

//Variable sanitation
if(!is_numeric($collid)) $collid = 0;
if(preg_match('/[^A-Z]+/', $namespace)) $namespace = '';
if(!in_array($generationMethod,array('inhouse','sesar'))) $generationMethod = '';
if(!is_numeric($processingCount)) $processingCount = 10;

$statusStr = '';
$isEditor = 0;
if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])){
	$isEditor = 1;
}
$guidManager = new OccurrenceSesar();
$guidManager->setCollid($collid);
$guidManager->setCollArr();
$guidManager->setSesarUser($username);
$guidManager->setSesarPwd($pwd);
$guidManager->setNamespace($namespace);
$guidManager->setGenerationMethod($generationMethod);

$sesarProfile = $guidManager->getSesarProfile();
if(isset($sesarProfile['namespace'])) $namespace = $sesarProfile['namespace'];
if(isset($sesarProfile['generationMethod'])) $generationMethod = $sesarProfile['generationMethod'];

if($action){
	if($action == 'saveProfile'){
		$guidManager->saveProfile();
	}
	elseif($action == 'deleteProfile'){
		$guidManager->deleteProfile();
		$namespace = '';
		$generationMethod = '';
	}
	elseif($action == 'verifyguid'){
		$guidManager->verifyLocalGuids();
	}
	elseif($action == 'verifysesar'){
		$guidManager->verifySesarGuids();
	}
}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title>IGSN GUID Management</title>
	<link rel="stylesheet" href="../../css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" />
	<link rel="stylesheet" href="../../css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" />
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript">
		function validateCredentials(f){
			if(f.username.value == "" || f.pwd.value == ""){
				alert("Please enter valid SESAR username and password");
				return false;
			}
			else if(f.username.value.indexOf("@") == -1){
				alert("SESAR username must be an email address");
				return false;
			}
			$.ajax({
				method: "POST",
				data: { username: f.username.value, password: f.pwd.value },
				dataType: "xml",
				url: "https://app.geosamples.org/webservices/credentials_service_v2.php"
			})
			.done(function(xml) {
				var valid = $(xml).find('valid').text();
				if(valid == "yes"){
					$(xml).find('user_codes').each(function(){
	                    $(this).find("user_code").each(function(){
	                        var userCode = $(this).text();
	                        $("#igsn-reg-div").show();
	                        $("#validate-button").hide();
	                        $("#valid-span").show();
	                        $("#notvalid-span").hide();
	                    });
	                });
				}
				else{
					alert($(xml).find('error').text());
	                $("#igsn-reg-div").hide();
	                $("#validate-button").show();
	                $("#valid-span").hide();
	                $("#notvalid-span").show();
				}
			})
			.fail(function() {
				alert("Validation call failed");
	            $("#igsn-reg-div").hide();
	            $("#validate-button").show();
	            $("#valid-span").hide();
	            $("#notvalid-span").show();
			});
		}

		function verifyProfileForm(f){
			if(f.namespace.value == ""){
				alert("Select a namespace");
				return false;
			}
			return true;
		}
	</script>
	<style type="text/css">
		fieldset{ margin:10px; padding:15px; }
		fieldset legend{ font-weight:bold; }
		.form-label{ font-weight: bold; }
		button{ margin:15px; }
	</style>
</head>
<body>
<?php
$displayLeftMenu = 'false';
include($SERVER_ROOT."/header.php");
?>
<div class='navpath'>
	<a href="../../index.php">Home</a> &gt;&gt;
	<a href="../misc/collprofiles.php?collid=<?php echo $collid; ?>&emode=1">Collection Management</a> &gt;&gt;
	<b>IGSN Management</b>
</div>
<!-- This is inner text! -->
<div id="innertext">
	<?php
	if($isEditor && $collid){
		echo '<h3>IGSN Management '.$guidManager->getCollectionName().'</h3>';
		if($statusStr){
			?>
			<fieldset style="margin:10px;">
				<legend>Error Panel</legend>
				<?php echo $statusStr; ?>
			</fieldset>
			<?php
		}
		if($action == ''){

		}
		/*
		Quality control
			- Test all IGSNs within the database to ensure they exist
			- Grab all SESAR IGSNs and make sure they are in the database (must work across all collections)
		*/
		if($namespace){
			$guidCnt = $guidManager->getGuidCount($collid);
			$guidMissingCnt = $guidManager->getMissingGuidCount();
			?>
			<fieldset>
				<legend>IGSN Profile Details</legend>
				<p>Occurrence IGSN GUID counts using the <?php echo $namespace; ?> namespace</p>
				<p>
					<b>GUIDs within collection:</b> <?php echo $guidCnt; ?>
				</p>
				<p>
					<b>GUIDs within all collections:</b> <?php echo $guidManager->getGuidCount(); ?>
				</p>
				<p>
					<b>Occurrences without GUIDs:</b> <?php echo $guidMissingCnt; ?>
				</p>
				<p>
					<span class="form-label">IGSN Namespace:</span>
					<?php echo $namespace; ?>
				</p>
				<p>
					<span class="form-label">IGSN generation method:</span>
					<?php echo $generationMethod; ?>
				</p>
			</fieldset>
			<fieldset>
				<legend>IGSN Profile Maintenance</legend>
				<form name="deleteform" action="igsnmanagement.php" method="post">
					<p>
						<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
						<button name="formsubmit" type="submit" value="deleteProfile">Delete Profile</button>
						<span style="margin-left:25px;"><a href="igsnmapper.php?collid=<?php echo $collid; ?>"><button name="formsubmit" type="button">Generate IGSN Identifiers</button></a></span>
					</p>
				</form>
			</fieldset>
			<fieldset>
				<legend>IGSN GUID Maintenance</legend>
				<form name="guidmaintenanceform" action="igsnmanagement.php" method="post">
					<p>Verify portal's IGSN GUIDs within database against the SESAR system and vice versa.</p>
					<p>
						<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
						<?php
						if($guidCnt){
							?>
							<button name="formsubmit" type="submit" value="verifyguid">Verify portal GUIDs</button>
							<?php
						}
						?>
						<span style="margin-left:25px;">
							<a href="igsnmapper.php?collid=<?php echo $collid; ?>"><button name="formsubmit" type="submit" value="verifysesar">Verify SESAR GUIDs</button></a>
						</span>
					</p>
				</form>
			</fieldset>
			<?php
		}
		else{
			?>
			<form name="profileform" action="igsnmanagement.php" method="post" onsubmit="return verifyProfileForm(this)">
				<fieldset>
					<legend>IGSN Registration Profile</legend>
					<p>
						<div>
							<span class="form-label">Username:</span> <input name="username" type="text" value="<?php echo $username; ?>" />
							<span id="valid-span" style="display:none;color:green">Credentials Valid!</span>
							<span id="notvalid-span" style="display:none;color:orange">Credentials Not Valid</span>
						</div>
						<div><span class="form-label">Password:</span> <input name="pwd" type="password" value="<?php echo $pwd; ?>" /></div>
						<button id="validate-button" type="button" onclick="validateCredentials(this.form)">Validate Credentials</button>
					</p>
					<div id="igsn-reg-div" style="margin-top:20px;">
						<p>
							<span class="form-label">IGSN Namespace:</span>
							<select name="namespace" onchange="generateIgsnSeed()">
								<option value="">-- Select an IGSN Namespace --</option>
								<option value="">------------------------------</option>
								<option value="NEO">NEO</option>
							</select>
						</p>
						<p>
							<span class="form-label">IGSN Generation Method:</span>
							<select name="generationMethod">
								<option value='sesar'>SESAR generates IGSN (recommended)</option>
								<option value='inhouse'>Generate IGSN in-house</option>
							</select>
						</p>
						<p>
							<button name="formsubmit" type="submit" value="saveProfile">Save Profile</button>
							<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
						</p>
					</div>
				</fieldset>
			</form>
			<?php
		}
	}
	else{
		echo '<h2>You are not authorized to access this page or collection identifier has not been set</h2>';
	}
	?>
</div>
<?php
include($SERVER_ROOT."/footer.php");
?>
</body>
</html>