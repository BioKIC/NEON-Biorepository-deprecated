<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/InstallationManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$installManager = new InstallationManager();
$verHistory = $installManager->getVersionHistory();
$currentVers = $installManager->getCurrentVersion();
?>
<html>
	<head>
		<title>Installation Assistant</title>
		<?php
		$activateJQuery = false;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<style type="text/css">
			label{ font-weight:bold }
		</style>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div id="innertext">
			<h1>Installation Assistant</h1>
			<div style="margin:15px;">
				<label>Current version: </label>
				<?php echo $currentVers; ?>
			</div>
			<div style="margin:15px">
				<table class="styledtable" style="width:300px;">
					<tr><th>Version</th><th>Date Applied</th></tr>
					<?php
					foreach($verHistory as $ver => $date){
						echo '<tr><td>'.$ver.'</td><td>'.$date.'</td></tr>';
					}
					?>
				</table>
			</div>
			<fieldset>
				<legend></legend>

			</fieldset>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
