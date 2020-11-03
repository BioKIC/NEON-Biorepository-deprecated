<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/OccHarvesterReports.php');
header("Content-Type: text/html; charset=".$CHARSET);

$reports = new OccHarvesterReports();
$reportsArr = $reports->getHarvestReport();
$headerArr = ['collid', 'sampleClass', 'errorMessage', 'count'];

$isEditor = false;
if($IS_ADMIN){
	$isEditor = true;
}
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Occurrence Harvester Reports</title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
		<?php
		$activateJQuery = true;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
            <style>
      table, ul {
        font-size: small; 
        text-align: left
        }

      td {
        color: #444444;
        padding: 1em;
        vertical-align: top;
        border-top: 2px solid #e7e7e7;
        border-bottom: 2px solid #e7e7e7;
        border-right: 0;
        border-left: 0;
      }

      tbody tr {
        max-width: 100%;
        width: 100%;
        border: none;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 0.75em;
      }

      tbody th:first-child td {
        border-top: 0;
      }

      tbody th {
        padding: 1em;
      }
    </style>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<a href="../index.php">NEON Biorepository Tools</a> &gt;&gt;
			<b>Occurrence Harvester Reports</b>
		</div>
		<div id="innertext">
			<?php
			if($isEditor){
				?>
        <?php 
        echo '<h1>Current Occurrence Harvester Errors</h1>';
        if(!empty($reportsArr)){
          $reportsTable = $reports->htmlTable($reportsArr, $headerArr);
          echo $reportsTable;
          };
          ?>
				<?php
			} else {
        echo '<h3>Please login to get access to this page.</h3>';
      }
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>