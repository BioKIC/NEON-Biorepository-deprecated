<?php
//error_reporting(E_ALL);
include_once("config/symbini.php");
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE?> Home</title>
	<?php include_once($SERVER_ROOT.'/headincludes.php'); ?>
	<meta name='keywords' content='' />
	<script type="text/javascript">
		<?php include_once('includes/googleanalytics.php'); ?>
	</script>
</head>
<body>
	<?php
	include($SERVER_ROOT."/header.php");
	?> 
        <!-- This is inner text! -->
        <div  id="innertext">
            <h1></h1>

            <div style="padding: 0px 10px;">
            	Description and introduction of project
            </div>
        </div>

	<?php
	include($SERVER_ROOT."/footer.php");
	?> 

</body>
</html>