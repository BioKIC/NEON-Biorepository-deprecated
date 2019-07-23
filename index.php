<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include_once('config/symbini.php');
include_once('content/lang/index.'.$LANG_TAG.'.php');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
header("Content-Type: text/html; charset=".$CHARSET);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE; ?> Home</title>
	<meta http-equiv="Expires" content="Tue, 01 Jan 1995 12:12:12 GMT">
	<meta http-equiv="Pragma" content="no-cache">
	<!-- META AND CSS -->
	<?php include_once($SERVER_ROOT.'/styles.php'); ?>
	<link href="css/quicksearch.css" type="text/css" rel="Stylesheet" />
	<link href="js/jquery-ui-1.12.1/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
	<!-- JS -->
<!-- 	<script src="js/jquery-3.2.1.min.js" type="text/javascript"></script>
	<script src="js/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<script src="js/symb/api.taxonomy.taxasuggest.js" type="text/javascript"></script> -->
	<script type="text/javascript">
		<?php include_once($SERVER_ROOT.'/config/googleanalytics.php'); ?>
	</script>
</head>
<body>
	<?php include($SERVER_ROOT.'/header.php'); ?>
	<!-- This is inner text! -->
	<div id="innertext" class="container" style="margin-top: 5%">
	    <h1 class="centered">Discover and access NEON sample-based data</h1>

	    <div class="row">
			<div class="four columns">
			    <h4 class="centered">Samples</h4>
			    <img src="https://imgplaceholder.com/420x120/cccccc/757575/glyphicon-stats" alt="ImgPlaceholder">
			</div>
			<div class="four columns">
			    <h4 class="centered">Taxa</h4>
			    <img src="https://imgplaceholder.com/420x120/cccccc/757575/glyphicon-stats" alt="ImgPlaceholder">
			</div>	      
			<div class="four columns">
			    <h4 class="centered">Preservation methods</h4>
			    <img src="https://imgplaceholder.com/420x120/cccccc/757575/glyphicon-list" alt="ImgPlaceholder">
			</div>      
	    </div>

	    <div class="row">	    
	    	<div class="six columns">
	    		<h2 class="centered">About the portal</h2>
	    		<p>The NEON Biorepository data portal allows one to</p>
	    		<ul>
	    			<li>Discover sample availability and suitability for focal research interests</li>
	    			<li>Initiate sample loan requests</li>
	    			<li>Contribute and publish value-added sample data</li>
	    		</ul>
	    		<p>The majority of the samples published here are physically housed at the <a href="https://biokic.asu.edu/collections">Arizona State University Biocollections</a>, located in Tempe, Arizona.</p>
	    	</div>
	    	<div class="six columns">
	    		<h2 class="centered">Learn more</h2>
	    		<p>Description</p>
	    		<p>Neon/Batelle</p>
	    		<p>Tutorials</p>
	    		<p>Contact</p>	
	    	</div>
	    </div>

	    <div class="row  centered">
	    	<h2>Discover</h2>
	    	<div class="three columns centered" style="background-color:#cccccc">
	    		<a href="https://imgplaceholder.com">
	    			<img src="https://imgplaceholder.com/100x100/cccccc/757575/glyphicon-search" alt="ImgPlaceholder">
	    			<p style="text-decoration: none;font-size:1.5rem;text-transform:uppercase">Text search</p>
	    		</a>
	    	</div>
	    	<div class="three columns centered" style="background-color:#cccccc">
	    		<a href="https://imgplaceholder.com">
	    			<img src="https://imgplaceholder.com/100x100/cccccc/757575/glyphicon-globe" alt="ImgPlaceholder">
	    			<p style="text-decoration: none;font-size:1.5rem;text-transform:uppercase;background-color:#cccccc">Map search</p>
	    		</a>
	    	</div>
	    	<div class="three columns centered" style="background-color:#cccccc">
	    		<a href="https://imgplaceholder.com">
	    			<img src="https://imgplaceholder.com/100x100/cccccc/757575/glyphicon-list-alt" alt="ImgPlaceholder">
	    			<p style="text-decoration: none;font-size:1.5rem;text-transform:uppercase;background-color:#cccccc">Species lists</p>
	    		</a>
	    	</div>
	    	<div class="three columns centered" style="background-color:#cccccc">
	    		<a href="https://imgplaceholder.com">
	    			<img src="https://imgplaceholder.com/100x100/cccccc/757575/glyphicon-tree-deciduous" alt="ImgPlaceholder">
	    			<p style="text-decoration: none;font-size:1.5rem;text-transform:uppercase;background-color:#cccccc">Taxonomic tree</p>
	    		</a>
	    	</div>	    		    		    	
	    </div>

	    <div class="row">
		    <h2 class="centered">Access</h2>
	    	<div class="four columns"><p>Visit the <a href="misc/usagepolicy.php">Data Usage Policy</a> page for information on how to cite data obtained from the NEON Biorepository Data Portal.</p></div>
	    	<div class="four columns"><p>Please consult the <a href="https://www.neonscience.org/data/archival-samples-specimens/neon-biorepository-asu">Archival Sample Request information page</a> to initiate inquiries about sample accessibility and loans.</p></div>
	    	<div class="four columns"><p>Join the portal as a regular visitor or contributor, and direct feedback or inquiries to <a href="mailto:BioRepo@asu.edu">BioRepo@asu.edu</a>.</p></div>
	    </div>

	</div>
	<?php include($SERVER_ROOT.'/footer.php'); ?>
</body>
</html>