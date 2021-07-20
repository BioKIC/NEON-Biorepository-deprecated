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
	<!-- UNIVERSAL CSS –––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/skeleton.css">
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
  <script type="text/javascript" src="<?php echo $CLIENT_ROOT.'/neon/js/d3.min.js'; ?>"></script>
</head>
<body class="home-page">
  <style>
    .bar:hover {
      opacity: 0.5;
    }
  </style>
	<?php include($SERVER_ROOT.'/includes/header.php'); ?>
	<!-- This is inner text! -->
	<div id="innertext" class="container" style="margin-top: 2rem">
		<h1 class="centered">Discover and access sample-based data</h1>
		<section>
      <div class="row">
				<img src="images/layout/Home-Map-2.jpg" alt="Map with samples collected within NEON sites" class="hide-on-small" style="width:100%;">
				<img src="images/layout/map-mobile.jpg" alt="Map with samples collected within NEON sites" class="hide-on-large">
				<p class="hide-on-small"><span style="font-size: 70%; line-height: 1">Samples available in the portal (Aug 2019), collected in Alaska (top left), Continental US (center), and Puerto Rico (bottom right). Colors indicate different collection types. Circle sizes indicate quantity of samples per collection in a given locality.</span></p>
				<p class="hide-on-large"><span style="font-size: 70%; line-height: 1">Samples available in the portal (Aug 2019), collected in Continental US (top), Alaska (bottom left), and Puerto Rico (bottom right). Colors indicate different collection types. Circle sizes indicate quantity of samples per collection in a given locality.</span></p>
			</div>
		</section>

		<section>
			<div class="row centered">
				<div class="four columns centered" style="background-color:#0071ce; color: white; margin-top:0.5em; padding: 0.4em 0">
					<a href="<?php echo $CLIENT_ROOT; ?>/collections/index.php" >
						<div>
							<img src="images/layout/glyphicon-search.png" alt="ImgPlaceholder" width="50px" height="50px" style="padding-top:0.5em;">
							<p style="text-decoration: none;font-size:1.2rem;background-color:#0071ce; color: white;">Sample search</p>
						</div>
					</a>
				</div>
				<div class="four columns centered" style="background-color:#0071ce; color: white; margin-top:0.5em; padding: 0.4em 0">
					<a href="<?php echo $CLIENT_ROOT; ?>/collections/map/index.php" target="_blank">
						<div>
							<img src="images/layout/glyphicon-globe.png" alt="ImgPlaceholder" width="50px" height="50px" style="padding-top:0.5em;">
							<p style="text-decoration: none;font-size:1.2rem;background-color:#0071ce; color: white;">Map search</p>
						</div>
					</a>
				</div>
				<div class="four columns centered" style="background-color:#0071ce; color: white; margin-top:0.5em; padding: 0.4em 0">
					<a href="<?php echo $CLIENT_ROOT; ?>/misc/checklists.php">
						<div>
							<img src="images/layout/glyphicon-list-alt.png" alt="ImgPlaceholder" width="50px" height="50px" style="padding-top:0.5em;">
							<p style="text-decoration: none;font-size:1.2rem;background-color:#0071ce; color: white;">Checklists</p>
						</div>
					</a>
				</div>
			</div>
		</section>

		<section>
			<div class="row" style="vertical-align: bottom">
				<div class="six columns centered">
					<h4 class="centered">> 103,000 samples</h4>
					<img src="images/layout/SamplesByColl-2020-01.png" usemap="#image-map" width="100%">
					<p><span style="font-size: 70%">Distribution of samples by collection type.</span></p>
          <div id="graph"></div>
				</div>
				<div class="six columns centered">
					<h4 class="centered">> 700 taxa</h4>
					<img src="images/layout/TaxaByGroup-2020-01.png" width="100%">
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
					<p>Please consult the <a href="https://www.neonscience.org/samples/find-samples">Archival Sample Request information page</a> to initiate inquiries about sample accessibility and loans.</p>
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
					<p>The majority of the NEON samples published here are physically housed at the <a href="https://biokic.asu.edu/collections" target="_blank">Arizona State University Biocollections</a>, located in Tempe, Arizona.</p>
					<div class="disclaimer">
						<p>The portal also integrates NEON samples with specimens from the same sites in external collections, allowing for research on biodiversity at NEON sites over a broader taxonomic and temporal extent.</p>
					</div>
				</div>
				<div class="six columns">
					<h2 class="centered">Learn more</h2>
					<p>This portal is offered through the <a href="https://bdj.pensoft.net/articles.php?id=1114" target="_blank">Symbiota</a> software platformand is informationally synchronized with the <a href="https://www.neonscience.org" target="_blank">main NEON Data Portal</a>, which serves the full spectrum of NEON data products.</p>
					<p>To learn more about the features and capabilities available through Symbiota, visit the <a href="http://symbiota.org/docs/" target="_blank">Symbiota Help Pages</a>.</p>
					<p>Read more about NEON's history and experimental design in the <a href="https://www.neonscience.org/about" target="_blank">main portal</a>.</p>
					<p>To explore sample collection and processing methods, visit the <a href="https://www.neonscience.org/data-collection/protocols-standardized-methods">NEON Protocols & Standardized Methods page</a>.</p>
				</div>
			</div>
		</section>

	</div>
	<?php include($SERVER_ROOT.'/includes/footer.php'); ?>
</body>
<script type="text/javascript" src="<?php echo $CLIENT_ROOT; ?>/neon/js/alerts.js?ver=1"></script>
<script type="text/javascript">
  let alerts = [{'alertMsg':'Try our <a href="./neon/search/index.php">New Occurrence Search Form!</a>'}];
  handleAlerts(alerts);
</script>
  <script>
  const colls = [
    {name: "Microbes", samples: "5000", db: "5,31,69,6"},
    {name: "Invertebrates", samples: "4500", db: "i"},
    {name: "Vertebrates", samples: "3000", db: "v"},
    {name: "Plants", samples: "500", db: "p"},
    {name: "Environmental", samples: "300", db: "e"},
    {name: "Algae", samples: "100", db: "a"}
  ]
  const cMin = d3.min(colls, (d) => d.samples);
  const cMax = d3.max(colls, (d) => d.samples);
  const cWidth = 300;
  const cHeight = 160;
  const cPadding = 0;
  const cYScale = d3.scaleLinear()
    .domain([0, cMax])
    .range([0, cHeight]);
  const cSvg = d3
    .select('#graph')
    .append('svg')
    .attr('viewBox', `0 0 ${cWidth} ${cHeight}`);
  cSvg.selectAll('rect')
    .data(colls)
    .enter()
    .append('a')
    .attr('xlink:href', (d) => `collections/list.php?db=${d.db}&includeothercatnum=1&usethes=1&taxontype=1`) // Adds url to text
    .attr('xlink:title', (d) => `Click to see ${d.samples} samples`) // Adds url to text
    .append('rect')
    .attr('title', (d, i) => d.samples)
    // .on('click', (d) => console.log(d))
    .attr('x', 0)
    .attr('y', (d, i) => 5 + i * 25)
    .attr('width', (d, i) => cYScale(d.samples))
    .attr('height', 20)
    .attr('fill', 'pink')
    .attr('class', 'bar');
  cSvg
    .selectAll('text')
    .data(colls)
    .enter()
    .append('text')
    .attr('x', (d, i) => 5 + cYScale(d.samples)) // all on right side
    .attr('y', (d, i) => 25 + i * 25) // adds gap on top
    .append('a') // Adds link element
    .attr('xlink:href', (d) => `collections/list.php?db=${d.db}&includeothercatnum=1&usethes=1&taxontype=1`) // Adds url to text
    .text((d, i) => d.name);

  </script>
</html>