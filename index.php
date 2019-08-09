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

	<script type="text/javascript">
		<?php include_once($SERVER_ROOT.'/config/googleanalytics.php'); ?>
	</script>
</head>
<body class="home-page">
	<?php include($SERVER_ROOT.'/header.php'); ?>
	<!-- This is inner text! -->
	<div id="innertext" class="container" style="margin-top: 8em">
		<h1 class="centered">Discover and access sample-based data</h1>

		<section>
			<div class="row">
				<img src="images/layout/Home-Map-2.jpg" alt="Map with samples collected within NEON sites" style="width:100%;">
				<p><span style="font-size: 70%; line-height: 1">Samples available in the portal (Aug 2019), collected in Alaska (top left), Continental US (center), and Puerto Rico (bottom right). Colors indicate different collection types. Circle sizes indicate quantity of samples per collection in a given locality.</span></p>
			</div>
		</section>

		<section>
			<div class="row centered">
				<div class="four columns centered" style="background-color:#0071ce; color: white; margin-top:0.5em; padding: 0.4em 0">
					<a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php" >
						<div>
							<img src="https://imgplaceholder.com/200x200/0071ce/ffffff/glyphicon-search" alt="ImgPlaceholder" width="50px" height="50px" style="padding-top:0.5em;">
							<p style="text-decoration: none;font-size:1.2rem;background-color:#0071ce; color: white;">Sample search</p>
						</div>
					</a>
				</div>
				<div class="four columns centered" style="background-color:#0071ce; color: white; margin-top:0.5em; padding: 0.4em 0">
					<a href="<?php echo $CLIENT_ROOT; ?>/collections/map/index.php" target="_blank">
						<div>
							<img src="https://imgplaceholder.com/200x200/0071ce/ffffff/glyphicon-globe" alt="ImgPlaceholder" width="50px" height="50px" style="padding-top:0.5em;">
							<p style="text-decoration: none;font-size:1.2rem;background-color:#0071ce; color: white;">Map search</p>	
						</div>
					</a>
				</div>
				<div class="four columns centered" style="background-color:#0071ce; color: white; margin-top:0.5em; padding: 0.4em 0">
					<a href="<?php echo $CLIENT_ROOT; ?>/projects/index.php?pid=1">
						<div>
							<img src="https://imgplaceholder.com/200x200/0071ce/ffffff/glyphicon-list-alt" alt="ImgPlaceholder" width="50px" height="50px" style="padding-top:0.5em;">
							<p style="text-decoration: none;font-size:1.2rem;background-color:#0071ce; color: white;">Checklists</p>
						</div>
					</a>
				</div>    		    		    	
			</div>
		</section>		

		<section>
			<div class="row" style="vertical-align: bottom">
				<div class="six columns centered">
					<h4 class="centered">> 62,000 samples</h4>
					<img src="images/layout/SamplesByColl.png" usemap="#image-map" width="100%">
					<!-- Image Map SamplesByColl -->
					<map name="image-map" width="auto">
					    <area target="" alt="Invertebrata Search" title="Invertebrata Search" href="https://biorepo.neonscience.org/portal/collections/list.php?db=21,11,39,14,22,13,16,29,4" coords="233,39,3986,156" shape="rect">
					    <area target="" alt="Environmental Search" title="Environmental Search" href="https://biorepo.neonscience.org/portal/collections/list.php?db=23,41,31,33,34,35,36,30" coords="226,177,3322,312" shape="rect">
					    <area target="" alt="Vertebrata Search" title="Vertebrata Search" href="https://biorepo.neonscience.org/portal/collections/list.php?db=20,12,15,24,17,25,26,27,28,19" coords="223,326,1802,467" shape="rect">
					    <area target="" alt="Plantae Search" title="Plantae Search" href="https://biorepo.neonscience.org/portal/collections/list.php?db=7,9,8,18,10" coords="223,485,675,619" shape="rect">
					    <area target="" alt="Microbia Search" title="Microbia Search" href="https://biorepo.neonscience.org/portal/collections/list.php?db=5,6" coords="223,634,622,772" shape="rect">
					</map>					
					<p><span style="font-size: 70%">Distribution of samples by collection type.</span></p>
				</div>
				<div class="six columns centered">
					<h4 class="centered">> 400 taxa</h4>
					<img src="images/layout/TaxaByGroup.png">
					<p><span style="font-size: 70%">Distribution of samples by top 5 determined taxa.</span></p>
				</div>

			</div>
		</section>

		<section>
			<div class="row">
				<div class="four columns">
					<h4 class="centered">Data</h4>
					<p>Visit the <a href="misc/usagepolicy.php">Data Usage Policy</a> page for information on how to cite data obtained from the NEON Biorepository Data Portal.</p>
				</div>
				<div class="four columns">
					<h4 class="centered">Specimens</h4>
					<p>Please consult the <a href="https://www.neonscience.org/data/archival-samples-specimens/neon-biorepository-asu">Archival Sample Request information page</a> to initiate inquiries about sample accessibility and loans.</p>
				</div>
				<div class="four columns">
					<h4 class="centered">Contact</h4>
					<p>Join the portal as a regular visitor or contributor, and send direct feedback or inquiries to <a href="mailto:BioRepo@asu.edu">BioRepo@asu.edu</a>.</p>
				</div>
			</div>
		</section>		

		<section>
			<div class="row">	    
				<div class="six columns">
					<h2 class="centered">Services</h2>
					<p>NEON Biorepository Data Portal services:</p>
					<ul>
						<li>Discover sample availability and suitability for focal research interests</li>
						<li>Initiate sample loan requests</li>
						<li>Contribute and publish value-added sample data</li>
					</ul>
					<p>The majority of the samples published here are physically housed at the <a href="https://biokic.asu.edu/collections" target="_blank">Arizona State University Biocollections</a>, located in Tempe, Arizona.</p>
				</div>
				<div class="six columns">
					<h2 class="centered">Learn more</h2>
					<p>This portal is offered through the <a href="https://bdj.pensoft.net/articles.php?id=1114" target="_blank">Symbiota</a> software platform, and informationally synchronized with the <a href="https://www.neonscience.org" target="_blank">main NEON Data Portal</a>, which serves the full spectrum of NEON data products.</p>
					<p>To learn more about the features and capabilities available through Symbiota, visit the <a href="http://symbiota.org/docs/" target="_blank">Symbiota Help Pages</a>.</p>
					<p>Read more about NEON's history and experimental design in the <a href="https://www.neonscience.org/about" target="_blank">main portal</a>.</p>
				</div>
			</div>
		</section>

	</div>
	<?php include($SERVER_ROOT.'/footer.php'); ?>
	<script type="text/javascript">
			(function($) {

		    // prevent errors in IE < 8
		    if (!$('<area coords=1>').attr('coords')) return;

		    // find all images attached to a map
		    var imgs = $('img[usemap]');

		    // store initial coords of each <area>
		    $('area').each(function(i, v) {
		        var area = $(v);
		        area.data('coords', area.attr('coords'));
		    });

		    // on window resize, iterate through each image
		    // and scale its map areas
		    $(window).bind('resize.scaleMaps', function() {
		        imgs.each(function(i, v) {
		            scaleMap($(v));
		        });
		    }).trigger('resize.scaleMaps');

		    // find image scale by comparing offset width with width attribute
		    // if the scale has changed or not set,
		    // scale its map's areas by this factor
		    // and store the new scale using $.data()
		    function scaleMap(img) {

		        var mapName  = img.attr('usemap').replace('#', ''),
		            map      = $('map[name="' + mapName + '"]'),
		            imgScale = img.width() / img.attr('width');

		        if (imgScale !== img.data('scale')) {
		            map.find('area').each(function(i, v) {
		                var area      = $(v),
		                    coords    = area.data('coords').split(','),
		                    newCoords = [];
		                $.each(coords, function(j, w) {
		                    newCoords.push(w * imgScale);
		                });
		                area.attr('coords', newCoords.join(','));
		            });
		            img.data('scale', imgScale);
		        }
		    }

		}(jQuery));
	</script>
</body>
</html>