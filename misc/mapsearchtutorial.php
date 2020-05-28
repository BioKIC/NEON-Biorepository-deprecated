<?php
include_once('../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
	<head>
		<title>Page Title</title>
    <?php
      $activateJQuery = false;
      if(file_exists($SERVER_ROOT.'/includes/head.php')){
        include_once($SERVER_ROOT.'/includes/head.php');
      }
      else{
        echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
        echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
        echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
      }
    ?>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
      <a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
      <a href="<?php echo $CLIENT_ROOT; ?>/misc/tutorials.php">Tutorials</a> &gt;&gt;   
			<b>View the Homepage</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
<h1 style="text-align: center;">Tutorials and Help</h1>


		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
