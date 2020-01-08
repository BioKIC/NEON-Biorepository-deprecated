<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Checklists</title>
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
			<b>Checklists</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
      <h1>Checklists</h1>
      <ul>
        <li><a href="https://biorepo.neonscience.org/portal/projects/index.php?pid=6">Research Sites - Invertebrates</a></li>
        <li><a href="https://biorepo.neonscience.org/portal/projects/index.php?pid=1"></a>Research Sites - Plants</li>
        <li><a href="https://biorepo.neonscience.org/portal/projects/index.php?pid=7"></a>Research Sites - Vertebrates</li>
      </ul>
		</div>
		<?php
			include($SERVER_ROOT.'/footer.php');
		?>
	</body>
</html>
