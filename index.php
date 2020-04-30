<?php
include_once('config/symbini.php');
//include_once('content/lang/index.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<?php
	$activateJQuery = false;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />
	<link href="js/jquery-ui-1.12.1/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script>
	<script type="text/javascript">
		<?php include_once($SERVER_ROOT.'/includes/googleanalytics.php'); ?>
	</script>
</head>
<body>
	<?php
	include($SERVER_ROOT.'/includes/header.php');
	?>
<table id="maintable" cellspacing="0">
    <tr>
		<td id='middlecenter'  colspan="3">
        <!-- This is inner text! -->
		<div id="innertext">
		<div style="float:right;margin-left:15px">
			<!--
			<div>
				<?php
				//---------------------------SLIDESHOW SETTINGS---------------------------------------
				//If more than one slideshow will be active, assign unique numerical ids for each slideshow.
				//If only one slideshow will be active, leave set to 1.
				$ssId = 1;
				//Enter number of images to be included in slideshow (minimum 5, maximum 10)
				$numSlides = 10;
				//Enter width of slideshow window (in pixels, minimum 275, maximum 800)
				$width = 350;
				//Enter amount of days between image refreshes of images
				$dayInterval = 7;
				//Enter amount of time (in milliseconds) between rotation of images
				$interval = 7000;
				//Enter checklist id, if you wish for images to be pulled from a checklist,
				//leave as 0 if you do not wish for images to come from a checklist
				//if you would like to use more than one checklist, separate their ids with a comma ex. "1,2,3,4"
				//$clid = '1279';
				$clid = '2';
				//Enter field, specimen, or both to specify whether to use only field or specimen images, or both
				$imageType = 'field';
				//Enter number of days of most recent images that should be included
				$numDays = 30;

				//---------------------------DO NOT CHANGE BELOW HERE-----------------------------
				//ini_set('max_execution_time', 120);
				//include_once($SERVER_ROOT.'/classes/PluginsManager.php');
				//$pluginManager = new PluginsManager();
				//echo $pluginManager->createSlideShow($ssId,$numSlides,$width,$numDays,$imageType,$clid,$dayInterval,$interval);
				?>
			//---------------------------END SLIDESHOW SETTINGS---------------------------------------
		</div>
			-->
		</div>
		<div style="padding: 0px 10px;font-size:120%">

			<h1 style="font-family:'Mate', serif">Welcome to the Consortium of California Herbaria Portal (CCH2)</h1>
			<div id="quicksearchdiv" style="float:right;padding: 30px 50px 10px 20px;font-family:'Mate', serif">
				<!-- -------------------------QUICK SEARCH SETTINGS--------------------------------------- -->
				<form name="quicksearch" id="quicksearch" action="<?php echo $CLIENT_ROOT; ?>/taxa/index.php" method="get" onsubmit="return verifyQuickSearch(this);">
					<div id="quicksearchtext" style="font-family:'Mate', serif">Search Taxon</div>
					<input id="taxa" type="text" name="taxon" />
					<button name="formsubmit"  id="quicksearchbutton" type="submit" value="Search Terms">Search</button>
				</form>
			</div>
			<p style="font-family:'Mate', serif">
				<b>CCH2</b> serves data from specimens housed in CCH member herbaria. These data are currently growing due to the work of the
				<b>California Phenology Thematic Collections Network</b> (<b>CAP-TCN;</b> <a href="https://www.capturingcaliforniasflowers.org" target="_blank">https://www.capturingcaliforniasflowers.org</a>).
				This collaboration of 22 California universities, research stations,
				natural history collections, and botanical gardens aims to capture images, label data, and phenological (i.e., flowering time)
				data from nearly 1 million herbarium specimens by 2022. Data contained in the CCH2 portal will continue to grow even after
				this time through the activities of the CCH member institutions.
			</p>
			<p style="font-family:'Mate', serif">
				The CCH2 portal is managed by UC Berkeley and Cal Poly, San Luis Obispo.
			</p>
			<div style="float:right"><img src="images/layout/UC1278733_small.jpg" style="width:200px;margin:0px 90px" /></div>
			<p style="font-family:'Mate', serif">	For more information about the California Consortium of Herbaria (CCH) see:</p>
			<div style="margin-left:15px"><p style="font-family:'Mate', serif"><a href="http://ucjeps.berkeley.edu/consortium/about.html" target="_blank">http://ucjeps.berkeley.edu/consortium/about.html</a></p></div>
            <div style="font-family:'Mate', serif">
            <b>Using CCH2 data:</b>
            </div>
			<div style="margin-top:15px;padding: 0px 10px;font-family:'Mate', serif">
				Please refer to our <a href="http://cch2.org/portal/misc/usagepolicy.php">Data Use Policy</a>. The Consortium of California Herbaria asks that users not redistribute data obtained from this site.
				However, links or references to this site may be freely posted. If you have any questions about this policy,
				please contact Jason Alexander (<a href="mailto:jason_alexander@berkeley.edu">jason_alexander@berkeley.edu</a>) or Katie Pearson (<a href="mailto:kdpearso@calpoly.edu">kdpearso@calpoly.edu</a>).
            </div>

			<div style="margin-top:15px;padding: 0px 10px;font-family:'Mate', serif">
            	<b>More California specimen data may be found at the following portals:</b>
				<ul>
                    <li>Only California vascular plants, linked to the statewide Jepson eFlora project: <a href="http://ucjeps.berkeley.edu/consortium/" target="_blank">CCH1 Portal</a></li>
					<li>Bryophytes: <a hrf="http://bryophyteportal.org" target="_blank">Consortium of North American Bryophyte Herbaria</a></li>
					<li>Fungi: <a href="http://mycoportal.org" target="_blank">Mycology Collections Portal (MyCoPortal)</a></li>
					<li>Lichens: <a hrf="http://lichenportal.org" target="_blank">Consortium of North American Lichen Herbaria</a></li>
					<li>Macroalgae: <a href="http://macroalgae.org" targert="_blank">Macroalgal Herbarium Consortium</a></li>
					<li>Pteridophytes: <a href="http://www.pteridoportal.org/portal/" target="_blank">Pteridophyte Collections Consortium</a></li>
				</ul>
			</div>
		</div>
	    </td>
	</tr>
</table>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
