<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Page Title</title>
		<?php include_once($SERVER_ROOT.'/headincludes.php'); ?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
			<b>Contactos</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">



		</div>
		<?php
			include($SERVER_ROOT.'/footer.php');
		?>
	</body>
</html>
