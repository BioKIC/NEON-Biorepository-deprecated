<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include_once('config/symbini.php');
include_once('content/lang/index.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<link href="css/base.css?ver=<?php echo $CSS_VERSION; ?>" type="text/css" rel="stylesheet" />
	<link href="css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />
	<link href="js/jquery-ui-1.12.1/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>
	<script type="text/javascript">
		<?php include_once($SERVER_ROOT.'/config/googleanalytics.php'); ?>
	</script>
	<style>
		#top-div{
			width: 100%;
			height: 250px;
			background-image:url("https://www.neonscience.org/sites/default/files/styles/slider/public/marquee-images/homepage4_0.jpg");
	        background-repeat: no-repeat !important;
	        background-position: 50.00% 34.65% !important;
	        background-size: 100% !important;
	        background-color: transparent !important;
		}
		#top-text{
			position: relative;
			top: 15%;
			margin-left: auto;
			margin-right: auto;
			color: white;
			width: 60%;
			font-weight: 300;
			text-shadow: 0 1px 1px rgba(0,0,0,0.5);
			font-family: "Source Sans Pro",Helvetica,Arial,sans-serif;
			font-size: 1.75rem;
			line-height: 36px;
			line-height: 2.25rem;
			text-align: center;
		}

		#top-button{
			margin-left: auto;
			margin-right: auto;
			margin-top: 25px;
			padding: 0.75rem 1.5rem;
			font-size: 1rem;
			line-height: 1.5rem;
			display: inline-block;
			vertical-align: middle;
			letter-spacing: 0.0625em;
			color: white;
			background-color: #005dab;
			font-weight: 600;
		}
	</style>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/header.php');
	?>
	<!-- This is inner text! -->
	<div id="top-div" style="clear: both">
		<div id="top-text">
			<strong>The National Ecological Observatory Network:</strong>
			Open data to understand how our aquatic and terrestrial ecosystems are changing.
			<div id="top-button" >Explore the Data Portal &gt;</div>
		</div>
	</div>
	<div  id="innertext">
		<div id="quicksearchdiv" style="float: right">
			<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
			<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
				<div id="quicksearchtext" ><?php echo (isset($LANG['QSEARCH_SEARCH'])?$LANG['QSEARCH_SEARCH']:'Search Taxon'); ?></div>
				<input id="taxa" type="text" name="taxon" />
				<button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON'])?$LANG['QSEARCH_SEARCH_BUTTON']:'Search'); ?></button>
			</form>
		</div>
		<div style="padding: 0px 10px;">
			More text about project
		</div>

		<?php
//---------------------------GAME SETTINGS---------------------------------------
//If more than one game will be active, assign unique numerical ids for each game.
//If only one game will be active, leave set to 1.
$oodID = 1;

//Enter checklist id (clid) of the checklist you wish to use, if you would like to use more than one checklist,
//separate their ids with a comma ex. "1,2,3,4"
$ootdGameChecklist = "2";

//Change to modify title
$ootdGameTitle = "Organism of the Day ";

//Replace "plant" with the type of organism, eg: plant, animal, insect, fungi, etc.
//This setting will appear in "Name that ______"
$ootdGameType = "plant";
//---------------------------DO NOT CHANGE BELOW HERE-----------------------------

include_once($SERVER_ROOT.'/classes/GamesManager.php');
$gameManager = new GamesManager();
$gameInfo = $gameManager->setOOTD($oodID,$ootdGameChecklist);
?>
<div style="float:right;margin-right:10px;width:290px;text-align:center;">
	<div style="font-size:130%;font-weight:bold;">
		<?php echo $ootdGameTitle; ?>
	</div>
	<a href="<?php echo $CLIENT_ROOT; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
		<img src="<?php echo $gameInfo['images'][0]; ?>" style="width:250px;border:0px;" />
	</a><br/>
	<b>What is this <?php echo $ootdGameType; ?>?</b><br/>
	<a href="<?php echo $CLIENT_ROOT; ?>/games/ootd/index.php?oodid=<?php echo $oodID.'&cl='.$ootdGameChecklist.'&title='.$ootdGameTitle.'&type='.$ootdGameType; ?>">
		Click here to test your knowledge
	</a>
</div>

	</div>
	<?php
	include($SERVER_ROOT.'/footer.php');
	?>
</body>
</html>