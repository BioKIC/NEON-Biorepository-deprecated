<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/TraitPlotManager.php');
include_once($SERVER_ROOT.'/classes/TraitPolarPlot.php');


Header("Content-Type: text/html; charset=".$CHARSET);

$tid = array_key_exists("tid",$_REQUEST)?$_REQUEST["tid"]:"";
$traitid = array_key_exists("traitid",$_REQUEST)?$_REQUEST["traitid"]:"";

if(!is_numeric($tid)) $tid = 0;
if(!is_numeric($traitid)) $traitid = 0;

$traitPlotter = new TraitPlotManager("polar");
if($tid) $traitPlotter->setTid($tid);
if($traitid) $traitPlotter->setTraitid($traitid);

// $polarPlot = new PolarPlot();
// $polarPlot->setAxisNumber(12);
// $polarPlot->setAxisRotation(15);
// $polarPlot->setTickNumber(3);
// $polarPlot->setAxisLabels(array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'));

?>

<html>
<head>
	<link rel="stylesheet" type="text/css" href="../../css/symb/taxa/traitplot.css">
	<style>
	.column {
  float: left;
  width: 50%;
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
	</style>
</head>
<body>
	<!-- array('id=1, type=polar, summary=bymonth, emphasize=2', 'id=3, type=bar, summary=byear'); -->
	<div class="row">
		<div class="column">
			<h2>
				<?php echo $traitPlotter->getSciname(); ?>
			</h2><h3>
				<?php echo $traitPlotter->getTraitName(); ?>
			</h3>
			<?php
				echo '<svg width="500" height="500" viewbox="0 0 ' . $traitPlotter->getViewboxWidth() . ' ' . $traitPlotter->getViewboxHeight() . ' role="img"><g>' . PHP_EOL;
				echo $traitPlotter->monthlyPolarPlot();
				//echo $traitPlotter->summarizeTraitByYear();
				echo '</g></svg>';
			?>
		</div>

		<div class="column">
</div>
</div>
</body>
</html>
