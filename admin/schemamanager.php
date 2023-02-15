<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SchemaManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$host = isset($_POST['host']) ? filter_var($_POST['host'], FILTER_SANITIZE_STRING) : 'localhost';
$username = isset($_POST['username']) ? filter_var($_POST['username'], FILTER_SANITIZE_STRING) : '';
$password = isset($_POST['password']) ? filter_var($_POST['password'], FILTER_SANITIZE_STRING) : '';
$database = isset($_POST['database']) ? filter_var($_POST['database'], FILTER_SANITIZE_STRING) : '';
$port = isset($_POST['port']) ? filter_var($_POST['port'], FILTER_SANITIZE_NUMBER_INT) : '3306';
$schemaPatch = isset($_POST['schemaPatch']) ? filter_var($_POST['schemaPatch'], FILTER_SANITIZE_STRING) : '';

$schemaManager = new SchemaManager();

$verHistory = $schemaManager->getVersionHistory();
$currentVerArr = $schemaManager->getCurrentVersion();
$curentVersion = $schemaManager->getCurrentVersion();

?>
<html>
	<head>
		<title>Database Schema Manager</title>
		<?php
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<style type="text/css">
			label{ font-weight:bold }
			fieldset legend{ font-weight:bold }
		</style>
	</head>
	<body>
		<?php
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div id="innertext">
			<h1>Database Schema Manager</h1>
			<div style="margin:15px;">
				<label>Current version: </label>
				<?php echo $currentVers; ?>
			</div>
			<div style="margin:15px">
				<table class="styledtable" style="width:300px;">
					<tr><th>Version</th><th>Date Applied</th></tr>
					<?php
					foreach($currentVerArr as $ver => $date){
						echo '<tr><td>'.$ver.'</td><td>'.$date.'</td></tr>';
					}
					?>
				</table>
			</div>
			<?php
			if($IS_ADMIN){
				if($action == 'installPatch'){
					$schemaManager->installPatch($host, $username, $password, $database, $port, $schemaPatch);
				}

			}
			?>
			<fieldset>
				<legend>Database Schema Assistant</legend>
				<div>Enter database criteria that will be used to apply database schema patch.
				The database user must have full DDL pivileges (e.g. create/alter tables, routines, indexes, etc.)
				We recommend creating a backup of the database before applying any database patches.</div>
				<form name="databaseMaintenanceForm" action="schemamanager.php" method="post">
					<div class="form-section">
						<label>Host:</label>
						<input name="database" type="text" value="<?php echo $host; ?>" required>
					</div>
					<div class="form-section">
						<label>Username:</label>
						<input name="username" type="text" value="<?php echo $username; ?>" required>
						<div>*Must have all DDL pivileges</div>
					</div>
					<div class="form-section">
						<label>Password: </label>
						<input name="password" type="password" value="" required>
					</div>
					<div class="form-section">
						<label>Database name:</label>
						<input name="database" type="text" value="<?php echo $database; ?>" required>
					</div>
					<div class="form-section">
						<label>Port:</label>
						<input name="port" type="text" value="<?php echo $port; ?>" required>
					</div>
					<div class="form-section">
						<label>Schema: </label>
						<select name="schemaPatch">
							<option value="1.0" <?php echo !$curentVersion || $curentVersion < 1 ? 'selected' : ''; ?>>Base Schema 1.0</option>
							<option value="1.1"<?php echo $curentVersion == 1.0 ? 'selected' : ''; ?>>Schema Patch 1.1</option>
							<option value="1.2"<?php echo $curentVersion == 1.1 ? 'selected' : ''; ?>>Schema Patch 1.2</option>
							<option value="2.0"<?php echo $curentVersion == 1.2 ? 'selected' : ''; ?>>Schema Patch 2.0</option>
						</select>
					</div>
					<div class="form-section">
						<button name="action" type="submit" value="installPatch">Install Patch</button>
					</div>
				</form>
			</fieldset>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
