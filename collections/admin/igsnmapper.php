<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceSesar.php');
header("Content-Type: text/html; charset=".$CHARSET);
ini_set('max_execution_time', 3600);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/admin/igsndmapper.php?'.$_SERVER['QUERY_STRING']);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$username = array_key_exists('username',$_REQUEST)?$_REQUEST['username']:'';
$pwd = array_key_exists('pwd',$_REQUEST)?$_REQUEST['pwd']:'';
$namespace = array_key_exists('namespace',$_REQUEST)?$_REQUEST['namespace']:'';
$registrationMethod = array_key_exists('registrationMethod',$_REQUEST)?$_REQUEST['registrationMethod']:'';
$generationMethod = array_key_exists('generationMethod',$_REQUEST)?$_REQUEST['generationMethod']:'';
$processingCount = array_key_exists('processingCount',$_REQUEST)?$_REQUEST['processingCount']:10;
$action = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

//Variable sanitation
if(!is_numeric($collid)) $collid = 0;
if(preg_match('/[^a-z0-9]/i', $namespace)) $namespace = '';
if(!in_array($registrationMethod,array('api','csv','xml'))) $registrationMethod = '';
if(!in_array($generationMethod,array('inhouse','sesar'))) $generationMethod = '';
if(!is_numeric($processingCount)) $processingCount = 10;

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
	                        $('select[name=namespace]').append(new Option(userCode, userCode))
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

		function verifyGuidForm(f){
			if(f.namespace.value == ""){
				alert("Select a SESAR namespace");
				return false;
			}
			else if(f.registrationMethod.value == ""){
				alert("Select a registration method");
				return false;
			}
			else if(f.generationMethod.value == ""){
				alert("Select an IGSN generation method");
				return false;
			}
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
	if($isEditor || $collid){
		?>
		<h3>IGSN Control Panel</h3>
		<div style="margin:10px;">

		</div>
		<?php
		if($action == 'populateGUIDs'){
			echo '<ul>';
			$guidManager->setNamespace($namespace);
			$guidManager->setRegistrationMethod($registrationMethod);
			if($generationMethod == 'inhouse') $guidManager->setGenerateIGSN(1);
			$guidManager->batchAssignIdentifiers($processingCount);
			echo '</ul>';
		}
		echo '<h3>'.$guidManager->getCollectionName().'</h3>';
		?>
		<p>
			<b>Occurrences without GUIDs:</b> <?php echo $guidManager->getMissingGuidCount(); ?>
		</p>
		<form name="guidform" action="igsnmapper.php" method="post" onsubmit="return verifyGuidForm(this)">
			<fieldset>
				<legend>IGSN Registration Control Panel</legend>
				<p>Register IGSNs via a manual submission of data file or automaticly using <a href="http://www.geosamples.org/interop" target="_blank">SESAR API Web Services</a></p>
				<p>
					<div>
						<span class="form-label">Username:</span> <input name="username" type="text" value="<?php echo $username; ?>" />
						<span id="valid-span" style="display:none;color:green">Credentials Valid!</span>
						<span id="notvalid-span" style="display:none;color:orange">Credentials Not Valid</span>
					</div>
					<div><span class="form-label">Password:</span> <input name="pwd" type="password" value="<?php echo $pwd; ?>" /></div>
					<button id="validate-button" type="button" onclick="validateCredentials(this.form)">Validate Credentials</button>
				</p>
				<div id="igsn-reg-div" style="display:none;margin-top:20px">
					<p>
						<span class="form-label">IGSN Namespace:</span>
						<select name="namespace">
							<option value="">-- Select an IGSN Namespace --</option>
							<option value="">------------------------------</option>
						</select>
					</p>
					<p>
						<span class="form-label">Registration method:</span>
						<select name="registrationMethod">
							<option value=''>-- Select Method --</option>
							<option value=''>----------------------------</option>
							<option value='api' <?php echo ($registrationMethod=='api'?'SELECTED':''); ?>>Batch API submission</option>
							<option value='csv' <?php echo ($registrationMethod=='csv'?'SELECTED':''); ?>>Manual CSV submission</option>
							<option value='xml' <?php echo ($registrationMethod=='xml'?'SELECTED':''); ?>>Manual XML submission</option>
						</select>
					</p>
					<p>
						<span class="form-label">IGSN generation method:</span>
						<select name="generationMethod">
							<option value=''>-- Select Method --</option>
							<option value=''>----------------------------</option>
							<option value='sesar' <?php echo ($generationMethod=='sesar'?'SELECTED':''); ?>>SESAR generates IGSN (recommended)</option>
							<option value='inhouse' <?php echo ($generationMethod=='inhouse'?'SELECTED':''); ?>>Generate IGSN in-house</option>
						</select>
					</p>
					<p>
						<span class="form-label">Number of identifiers to generate: </span>
						<input name="processingCount" type="text" value="10" /> (leave blank to process all specimens)
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
		echo '<h2>You are not authorized to access this page or collection identifier has not been set</h2>';
	}
	?>
</div>
<?php
include($SERVER_ROOT."/footer.php");
?>
</body>
</html>