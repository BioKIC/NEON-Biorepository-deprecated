<?php
//error_reporting(E_ALL);
include_once("config/symbini.php");
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE?> Home</title>
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
	<meta name='keywords' content='' />
	<script type="text/javascript">
		<?php include_once('includes/googleanalytics.php'); ?>
	</script>
</head>
<body>
	<?php
	$displayLeftMenu = "true";
	include($SERVER_ROOT.'/includes/header.php');
	?> 
        <!-- This is inner text! -->
        <div  id="innertext">
            <h1></h1>

            <div style="margin:20px;">
            	Description and introduction of project
            </div>
        </div>

	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?> 

</body>
</html>