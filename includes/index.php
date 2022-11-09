<?php
include_once('../config/symbini.php');
header('Content-Type: text/html; charset=' . $CHARSET);
header('Location: ' . $CLIENT_ROOT . '/index.php');
include_once($SERVER_ROOT . '/content/lang/prohibit.' . $LANG_TAG . '.php');

?>
<html>

<head>
	<title><?php echo (isset($LANG['FORBIDDEN']) ? $LANG['FORBIDDEN'] : 'Forbidden'); ?></title>
	<?php
	$activateJQuery = false;
	if (file_exists($SERVER_ROOT . '/includes/head.php')) {
		include_once($SERVER_ROOT . '/includes/head.php');
	} else {
		echo '<link href="' . $CLIENT_ROOT . '/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="' . $CLIENT_ROOT . '/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
		echo '<link href="' . $CLIENT_ROOT . '/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	?>
</head>

<body>
	<?php
	$displayLeftMenu = false;
	include($SERVER_ROOT . '/includes/header.php');
	?>
	<!-- This is inner text! -->
	<div id="innertext">
		<h1><?php echo (isset($LANG['FORBIDDEN']) ? $LANG['FORBIDDEN'] : 'Forbidden'); ?></h1>
		<div style="font-weight:bold;">
			<?php echo (isset($LANG['NO_PERMISSION']) ? $LANG['NO_PERMISSION'] : 'You don\'t have permission to access this page.'); ?>
		</div>
		<div style="font-weight:bold;margin:10px;">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php"><?php echo (isset($LANG['RETURN']) ? $LANG['RETURN'] : 'Return to index page'); ?></a>
		</div>
	</div>
	<?php
	include($SERVER_ROOT . '/includes/footer.php');
	?>
</body>

</html>