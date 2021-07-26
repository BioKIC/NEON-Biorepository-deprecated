<?php
include_once ('../../config/symbini.php');
include_once ($SERVER_ROOT . '/classes/GeographicThesaurus.php');
header("Content-Type: text/html; charset=" . $CHARSET);

if (! $SYMB_UID)
    header('Location: ../profile/index.php?refurl=../collections/georef/thesaurus.php?' . htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$submitAction = array_key_exists('submitaction', $_POST) ? $_POST['submitaction'] : '';

// Sanitation
$submitAction = filter_var($submitAction, FILTER_SANITIZE_STRING);

$geoManager = new GeographicThesaurus();

$isEditor = false;
if ($IS_ADMIN)
    $isEditor = true;

$statusStr = '';
if ($isEditor && $submitAction) {
    if ($submitAction == 'Update Coordinates')
        $statusStr = $geoManager->updateCoordinates($_POST);
}
?>
<html>
<head>
<title><?php echo $DEFAULT_TITLE; ?> - Geographic Thesaurus Manager</title>
		<?php
$activateJQuery = true;
include_once ($SERVER_ROOT . '/includes/head.php');
?>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js"
	type="text/javascript"></script>
<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js"
	type="text/javascript"></script>
</head>
<body>
	<div id='innertext'></div>
</body>
</html>