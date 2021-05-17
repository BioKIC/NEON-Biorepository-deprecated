<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceDataset.php');
header("Content-Type: text/html; charset=".$CHARSET);

// Datasets
$datasetid = array_key_exists('datasetid',$_REQUEST)?$_REQUEST['datasetid']:0;

if(!is_numeric($datasetid)) $datasetid = 0;

$datasetManager = new OccurrenceDataset();
$dArr = $datasetManager->getPublicDatasetMetadata($datasetid);
$searchUrl = '../../collections/list.php?datasetid='.$datasetid;
$ocArr = $datasetManager->getOccurrences($datasetid);
?>
<html>
	<head>
		<title>Dataset: <?php echo $dArr['name'] ;?></title>
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
			<b>Dataset: <?php echo $dArr['name'] ;?></b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
    <h1>Dataset: <?php echo $dArr['name'] ;?></h1>
    <ul>
      <!-- Metadata -->
      <p><?php echo $dArr['notes'] ;?></p>
      <!-- Occurrences Summary -->
      <p>This dataset includes <?php echo count($ocArr); ?> records.</p>
      
      <p><a href="<?php echo $searchUrl ;?>">View and download list of samples in this Dataset</a></p>
      <!-- REUSE http://github.localhost:8080/Symbiota-light-BioKIC/collections/list.php?datasetid=151 ?-->

    </ul>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
