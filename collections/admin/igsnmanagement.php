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

if($action){
	if($action == 'saveProfile'){
		$guidManager->saveProfile();
	}
	elseif($action == 'deleteProfile'){
		$guidManager->deleteProfile();
		$namespace = '';
		$generationMethod = '';
	}
}

$sesarProfile = $guidManager->getSesarProfile();
if(isset($sesarProfile['namespace'])) $namespace = $sesarProfile['namespace'];
if(isset($sesarProfile['generationMethod'])) $generationMethod = $sesarProfile['generationMethod'];
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
		.form-label{  }
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
	<a href="igsnmapper.php?collid=<?php echo $collid; ?>">IGSN GUID Generator</a> &gt;&gt;
	<b>IGSN Management</b>
</div>
<!-- This is inner text! -->
<div id="innertext">
	<?php
	if($isEditor && $collid){
		echo '<h3>IGSN Management '.$guidManager->getCollectionName().'</h3>';
		if($statusStr){
			?>
			<fieldset>
				<legend>Error Panel</legend>
				<?php echo $statusStr; ?>
			</fieldset>
			<?php
		}
		if($action){
			echo '<fieldset><legend>Action Panel</legend>';
			echo '<ul>';
			if($action == 'verifysesar'){
				echo '<li>Verifying all IGSNs located within SESAR system against portal database...</li>';
				$sesarArr = $guidManager->verifySesarGuids();
				echo '<li style="margin-left:10px">Checked '.$sesarArr['checkedCnt'].' out of '.$sesarArr['totalCnt'].' GUIDs</li>';
				if(isset($sesarArr['collid'])){
					echo '<li style="margin-left:10px">Registered IGSNs by Collection:</li>';
					foreach($sesarArr['collid'] as $id => $collArr){
						echo '<li style="margin-left:20px"><a href="../misc/collprofiles.php?collid='.$id.'" target="_blank">'.$collArr['name'].'</a>: '.$collArr['cnt'].' IGSNs</li>';
					}
				}
				$missingCnt = 0;
				if(isset($sesarArr['missing'])) $missingCnt = count($sesarArr['missing']);
				echo '<li style="margin-left:10px">';
				echo '# IGSNs not in database: '.$missingCnt;
				if($missingCnt) echo ' <a href="#" onclick="$(\'#missingGuidList\').show();return false;">(display list)</a>';
				echo '</li>';
				if($missingCnt){
					echo '<div id="missingGuidList" style="margin-left:25px;display:none">';
					foreach($sesarArr['missing'] as $igsn){
						echo '<li><a href="https://sesardev.geosamples.org/sample/igsn/'.$igsn.'" target="_blank">'.$igsn.'</a></li>';
					}
					echo '</div>';
				}
				echo '<li style="margin-left:10px">Finished verifying GUIDs!</li>';
				echo '</ul>';
				ob_flush();
				flush();

				echo '<ul style="margin-top:15px">';
				echo '<li>Starting to verify portal\'s IGSNs against SESAR system...</li>';
				$localArr = $guidManager->verifyLocalGuids();
				echo '<li style="margin-left:10px"># IGSNs checked: '.$localArr['cnt'].'</li>';
				$missingCnt = 0;
				if(isset($localArr['missing'])) $missingCnt = count($localArr['missing']);
				echo '<li style="margin-left:10px">';
				echo '# of unmapped IGSNs: '.$missingCnt;
				if($missingCnt) echo ' <a href="#" onclick="$(\'#unmappedGuidList\').show();return false;">(display list)</a>';
				echo '</li>';
				if($missingCnt){
					echo '<div id="unmappedGuidList" style="margin-left:25px;display:none">';
					foreach($localArr['missing'] as $occid => $guid){
						echo '<li><a href="../individual/index.php?occid='.$occid.'" target="_blank">'.$guid.'</a></li>';
					}
					echo '</div>';
				}
				echo '<li style="margin-left:10px">Finished verifying local IGSN GUIDs!</li>';
			}
			echo '</ul>';
			echo '</fieldset>';
		}
		/*
		Quality control
			- Test all IGSNs within the database to ensure they exist
			- Grab all SESAR IGSNs and make sure they are in the database (must work across all collections)
		*/
		if($namespace){
			$guidCnt = $guidManager->getGuidCount($collid);
			$guidMissingCnt = $guidManager->getMissingGuidCount();
			$guidAllCollCnt = $guidManager->getGuidCount();
			?>
			<fieldset>
				<legend>IGSN Profile Details & Statistics</legend>
				<p><span class="form-label">IGSN Namespace:</span> <?php echo $namespace; ?></p>
				<p><span class="form-label">IGSN generation method:</span> <?php echo $generationMethod; ?></p>
				<p><span class="form-label">GUIDs within collection:</span> <?php echo $guidCnt; ?></p>
				<p><span class="form-label">Occurrences without GUIDs:</span> <?php echo $guidMissingCnt; ?></p>
				<?php
				if($guidAllCollCnt > $guidCnt){
					?>
					<p><span class="form-label">GUIDs using above namespace across all collections:</span> <?php echo $guidAllCollCnt; ?></p>
					<?php
				}
				?>
				<div style="margin:10px;">
					<form name="deleteform" action="igsnmanagement.php" method="post">
						<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
						<input type="hidden" name="namespace" value="<?php echo $namespace; ?>" />
						<span style="margin-left:10px;">
							<button name="formsubmit" type="submit" value="verifysesar">Verify SESAR GUIDs</button>
						</span>
						<span style="margin-left:10px;">
							<button name="formsubmit" type="submit" value="deleteProfile" onclick="return confirm('Are you sure you want to delete this profile?')">Delete Profile</button>
						</span>
						<span style="margin-left:10px;">
							<a href="igsnmapper.php?collid=<?php echo $collid; ?>"><button type="button">Go to Mapper</button></a>
						</span>
					</form>
				</div>
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