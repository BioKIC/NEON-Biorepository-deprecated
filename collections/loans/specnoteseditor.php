<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
header("Content-Type: text/html; charset=".$CHARSET);
if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../collections/loans/specimennotes.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = $_REQUEST['collid'];
$occid = $_REQUEST['occid'];
$loanID = $_REQUEST['loanid'];
$notes = array_key_exists('notes',$_POST)?$_POST['notes']:'';
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

//Sanitation
if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($occid)) $occid = 0;
if(!is_numeric($loanID)) $loanID = 0;
$notes = filter_var($notes, FILTER_SANITIZE_STRING);

$isEditor = 0;
if($SYMB_UID && $collid){
	if($IS_ADMIN || (array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))
		|| (array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollEditor']))){
		$isEditor = 1;
	}
}

$loanManager = new OccurrenceLoans();
$loanManager->setCollId($collid);
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>">
	<title><?php echo $DEFAULT_TITLE; ?> Loan Specimen Notes Editor</title>
	<?php
	$activateJQuery = true;
	if(file_exists($SERVER_ROOT.'/includes/head.php')){
		include_once($SERVER_ROOT.'/includes/head.php');
	}
	else{
		echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">
		function submitNotesForm(){
			self.close();
		}

	</script>
	<style>
		fieldset{ padding:10px }
		fieldset legend{ font-weight:bold }
	</style>
</head>
<body>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($isEditor && $collid){
			$noteStr = $loanManager->getSpecimenNotes($loanID, $occid)
			?>
			<fieldset class="notesDiv" >
				<legend>Loan Specimen Editor</legend>
				<form name="noteEditor" action="outgoing.php" method="post" target="parentWin" onsubmit="submitNotesForm()">
					<b>Specimen Notes</b><br/>
					<input name="notes" type="text" value="<?php echo $noteStr; ?>" style="width:100%" />
					<input name="loanid" type="hidden" value="<?php echo $loanID; ?>" />
					<input name="occid" type="hidden" value="<?php echo $occid; ?>" />
					<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
					<input name="tabindex" type="hidden" value="1" />
					<button name="formsubmit" type="submit" value="saveSpecimenNotes">Save Edits</button>
				</form>
			</fieldset>
			<?php
		}
		else{
			if(!$isEditor) echo '<h2>You are not authorized to manage loans for this collection</h2>';
			else echo '<h2>ERROR: unknown error, please contact system administrator</h2>';
		}
		?>
	</div>
</body>
</html>