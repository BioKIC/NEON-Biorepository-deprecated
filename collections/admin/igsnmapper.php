<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSesar.php');
header("Content-Type: text/html; charset=".$CHARSET);
ini_set('max_execution_time', 3600);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/igsndmapper.php?'.$_SERVER['QUERY_STRING']);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$username = array_key_exists('username',$_REQUEST)?$_REQUEST['username']:'';
$pwd = array_key_exists('pwd',$_REQUEST)?$_REQUEST['pwd']:'';
$action = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

$isEditor = 0;
if($IS_ADMIN || array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])){
	$isEditor = 1;
}

$guidManager = new OccurrenceSesar();
$guidManager->setCollid($collid);
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
			$.ajax({
				method: "POST",
				data: { username: f.username.value, password: f.pwd.value },
				dataType: "xml",
				url: "https://app.geosamples.org/webservices/credentials_service_v2.php"
			})
			.done(function(xml) {
				var valid = $(xml).find('valid').text();
				if(valid == "yes"){
					alert("valid user");
					$(xml).find('user_codes').each(function(){
	                    $(this).find("user_code").each(function(){
	                        var userCode = $(this).text();
	                        alert(userCode);
	                        $("#igsn-reg-div").show();
	                    });
	                });
				}
				else{
					alert($(xml).find('error').text());
                    $("#igsn-reg-div").hide();
				}
			})
			.fail(function() {
				alert("Validation call failed");
			});
		}

		function verifyGuidForm(f){

			return true;
		}

		function verifyGuidAdminForm(f){

			return true;
		}
	</script>
	<style type="text/css">
		fieldset{ margin:10px }
		.form-label{ font-weight: bold; }
		button{ margin:15px; }
	</style>
</head>
<body>
<?php
$displayLeftMenu = 'false';
include($SERVER_ROOT."/header.php");
?>
<!-- This is inner text! -->
<div id="innertext">
	<?php
	if($isEditor){
		?>
		<h3>IGSN Control Panel</h3>
		<div style="margin:10px;">

		</div>
		<?php
		if($action == 'populateIgsnGuids'){
			echo '<ul>';
			$guidManager->populateGuids();
			echo '</ul>';
		}

		if($collid) echo '<h3>'.$guidManager->getCollectionName().'</h3>';
		?>
		<p>
			<b>Occurrences without GUIDs:</b> <?php echo $guidManager->getMissingGuidCount(); ?>
		</p>
		<form name="guidform" action="igsnmapper.php" method="post" onsubmit="return verifyGuidForm(this)">
			<fieldset>
				<legend>Manual Registration</legend>
				<p>Generate registration documents for manual submittions</p>
				<p>
					<span class="form-label">Registration method:</span>
					<select name="registrationMethod">
						<option value=''>-- Select Method --</option>
						<option value=''>----------------------------</option>
						<option value='csv'>Batch CSV submission</option>
						<option value='xml'>Batch XML submission</option>
					</select>
				</p>
				<p>
					<span class="form-label">IGSN generation method:</span>
					<select name="generationMethod">
						<option value=''>-- Select Method --</option>
						<option value=''>----------------------------</option>
						<option value='sesar'>SESAR generates IGSN (recommended)</option>
						<option value='inhouse'>Generate IGSN in-house</option>
					</select>
				</p>
				<p>
					<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
					<button type="submit" name="formsubmit" value="populateGUIDs">Populate Collection GUIDs</button>
				</p>
			</fieldset>
		</form>
		<form name="guidform" action="igsnmapper.php" method="post" onsubmit="return verifyGuidForm(this)">
			<fieldset>
				<legend>Automatic Registration</legend>
				<p>Register IGSN automatically using the <a href="http://www.geosamples.org/interop" target="_blank">SESAR API Services</a></p>
				<p>
					<div><span class="form-label">Username:</span> <input name="username" type="text" value="<?php echo $username; ?>" /></div>
					<div><span class="form-label">Password:</span> <input name="pwd" type="password" value="<?php echo $pwd; ?>" /></div>
					<button type="button" onclick="validateCredentials(this.form)">Validate Credentials</button>
				</p>
				<div id="igsn-reg-div" style="display:none">
					<p>
						<span class="form-label">IGSN generation method:</span>
						<select name="generationMethod">
							<option value=''>-- Select Method --</option>
							<option value=''>----------------------------</option>
							<option value='sesar'>SESAR generates IGSN (recommended)</option>
							<option value='inhouse'>Generate IGSN in-house</option>
						</select>
					</p>
					<p>
						<span class="form-label">Number of identifiers to generate: </span>
						<input name="count" type="text" value="10" /> (leave blank to process all specimens)
					</p>
					<p>
						<input type="hidden" name="collid" value="<?php echo $collid; ?>" />
						<button type="submit" name="formsubmit" value="populateGUIDs">Populate Collection GUIDs</button>
					</p>
				</div>
			</fieldset>
		</form>
		<?php
	}
	else{
		echo '<h2>You are not authorized to access this page</h2>';
	}
	?>
</div>
<?php
include($SERVER_ROOT."/footer.php");
?>
</body>
</html>