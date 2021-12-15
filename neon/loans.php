<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
include_once($SERVER_ROOT.'/neon/classes/Utilities.php');
header("Content-Type: text/html; charset=".$CHARSET);

$reports = new OccurrenceLoans();
$utilities = new Utilities();
$loansArr = $reports->getLoanOutAll();
$headerArr = ['loanid','collection','borrower','forwhom','datesent','datedue','dateclosed','numspecimens','enteredby'];
$total = $reports->getOutSamplesCnt();

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif(array_key_exists('CollAdmin',$USER_RIGHTS) || array_key_exists('CollEditor',$USER_RIGHTS)) $isEditor = true;
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Loans Reports</title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
		<?php
		$activateJQuery = true;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
    <link rel="stylesheet" href="css/tables.css">
		<script src="../../js/jquery-3.2.1.min.js" type="text/javascript"></script>
		<script src="../../js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	</head>
	<body>
		<?php
		$displayLeftMenu = false;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<a href="../index.php">NEON Biorepository Tools</a> &gt;&gt;
			<b>Loans Reports</b>
		</div>
		<div id="innertext">
			<?php
			if($isEditor){
				?>
        <?php
        echo '<h1>Loans Reports</h1>';
        echo '<p>Total number of samples in open loans: '.$total.'</p>';
        echo '<p class="helper"> <svg class="MuiSvgIcon-root jss173 MuiSvgIcon-fontSizeLarge" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg> Click columns names to sort (click again to toggle ascending/descending). Click "Loan ID" to check loan.</p>';
        ?>
        <?php
          if(!empty($loansArr)){
            $loansTable = $utilities->htmlTable($loansArr, $headerArr);
            echo $loansTable;
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
  <script src="js/sortables.js"></script>
</html>