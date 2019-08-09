<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Sample Use Request</title>
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
			<b>Sample Use Request</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext" style="text-align: center;">
			<h1>Sample Use Request</h1>
			<iframe src="https://docs.google.com/forms/d/e/1FAIpQLSffl3bMpsRdkK-Q7VpovBXDTFnuyAv8snO74HFm7owEEZdv3Q/viewform?embedded=true" width="790" height="1000px" frameborder="0" marginheight="0" marginwidth="0" style="margin-top: 2rem">Loading…</iframe></iframe>
		</div>
		<?php
			include($SERVER_ROOT.'/footer.php');
		?>
	</body>
</html>
