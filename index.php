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
	<!-- CSS -->
	<link href="css/base.css" type="text/css" rel="stylesheet" />
	<link href="css/main.css<?php echo (isset($CSS_VERSION_LOCAL)?'?ver='.$CSS_VERSION_LOCAL:''); ?>" type="text/css" rel="stylesheet" />
	<link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />

	<!-- JS -->
	<link href="js/jquery-ui-1.12.1/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>
	<script type="text/javascript">
		<?php include_once($SERVER_ROOT.'/config/googleanalytics.php'); ?>
	</script>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/header.php');
	?>
	<!-- This is inner text! -->
	<div  id="innertext">
		<!-- Intro -->
		<div id="home-left">
			<h2>Welcome to the NEON Biorepository Data Portal</h2>
			<p>The National Ecological Observatory Network - NEON Biodiversity Data Portal is being developed to provide innovative access and discoverability to the entirety of NEON’s sample-based data products; including nearly 40 vertebrate, invertebrate, plant, microbial, and environmental sample collections. The majority of these samples are physically housed at the Arizona State University Biocollections, located in Tempe, Arizona.</p>
			<p>The portal is being designed to allow current and prospective NEON researchers to interactively explore and understand sample availability and suitability for focal research interests, to initiate research sample loan requests, and to contribute and publish value-added sample data both directly in the portal and through emerging integration services with external data publishers.</p>
			<p>The NEON Biorepository Data Portal is offered through the Symbiota software platform, and informationally synchronized with the main NEON Data Portal which serves the full spectrum of NEON data products. To learn more about the features and capabilities available through Symbiota, visit the Symbiota Help Pages. Join the portal as a regular visitor or contributor, and direct feedback or inquiries to <a href="mailto:BioRepo@asu.edu">BioRepo@asu.edu</a>. Visit the <a href="#">Data Usage Policy</a> page for information on how to cite data obtained from the NEON Biorepository Data Portal. Please consult the Sample Use Policy to initiate inquiries about sample accessibility and loans.</p>
			<p style="display:none;">The NEON Biorepository Data Portal currently serves over xxx samples and xxx individual occurrence records; corresponding to xxx species, and xxx images (April 15, 2019).</p>
		</div>
		<div id="home-right">
			<!-- Quick search -->
			<div id="quicksearchdiv" >
				<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
				<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
					<div id="quicksearchtext" ><?php echo (isset($LANG['QSEARCH_SEARCH'])?$LANG['QSEARCH_SEARCH']:'Search Taxon'); ?></div>
					<input id="taxa" type="text" name="taxon" />
					<button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms"><?php echo (isset($LANG['QSEARCH_SEARCH_BUTTON'])?$LANG['QSEARCH_SEARCH_BUTTON']:'Search'); ?></button>
				</form>
			</div>
			<?php
			//---------------------------GAME SETTINGS---------------------------------------
			//If more than one game will be active, assign unique numerical ids for each game.
			//If only one game will be active, leave set to 1.
			$oodID = 1;

			//Enter checklist id (clid) of the checklist you wish to use, if you would like to use more than one checklist,
			//separate their ids with a comma ex. "1,2,3,4"
			$ootdGameChecklist = "98";

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
			<div>
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



		</div>

	<?php
	include($SERVER_ROOT.'/footer.php');
	?>
</body>
</html>