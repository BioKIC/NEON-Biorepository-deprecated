<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDataset.php');
header("Content-Type: text/html; charset=".$CHARSET);

$datasetManager = new OccurrenceDataset();

$dArr = $datasetManager->getPublicDatasets();

?>
<html>
	<head>
		<title>Public Datasets List</title>
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
			<b>Public Datasets List</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
    <h1>Public Datasets List</h1>
    <ul>
      <?php 
        // print_r($dArr);
        if($dArr){
          foreach($dArr as $row) {
            echo '<li><a href="../public.php?datasetid='.$row['datasetid'].'">'.$row['name'].'</a></li>';
          }
        }
       ;?>
    </ul>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
