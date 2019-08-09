<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Sample Use Policy</title>
		<link href="<?php echo $CLIENT_ROOT; ?>/css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
		<link href="<?php echo $CLIENT_ROOT; ?>/css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
			<b>Sample Use Policy</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext" style="text-align: center;">
			<h1>Sample Use Policy</h1>
			<iframe src="https://docs.google.com/document/d/e/2PACX-1vQr_AfNncaTDEJH47zOWuaO-CRbUsbb27xnnM6KFOV7jpyJoYUYuDbkEO4efPBbXOG9fiX0JpdJcdU2/pub?embedded=true" width="790px" height="600px" frameborder="0" marginheight="0" marginwidth="0"></iframe>
		</div>
		<?php
			include($SERVER_ROOT.'/footer.php');
		?>
	</body>
</html>
