<?php
  include_once('../config/symbini.php');
  include_once($SERVER_ROOT.'/neon/classes/Sources.php');
  header("Content-Type: text/html; charset=".$CHARSET);

  $sources = new Sources();
?>
<html>
	<head>
		<title>Taxonomies</title>
    <?php
      $activateJQuery = false;
      if(file_exists($SERVER_ROOT.'/includes/head.php')){
        include_once($SERVER_ROOT.'/includes/head.php');
      }
      else{
        echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
        echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
        echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
      }
    ?>

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
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="<?php echo $CLIENT_ROOT; ?>/index.php">Home</a> &gt;&gt;
			<b>Taxonomies</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
    <h1>Taxonomies</h1>
    <p>The Biorepository Data Portal is a Symbiota portal that makes available sample-based information from both non-biological (in the Environmental Samples category) and biological collections (categories Algae, Invertebrates, Microbes, Plants, and Vertebrates).</p>
    <p>Although most of the data hosted in this portal pertains to physical samples maintained at the NEON Biorepository at the Arizona State University, users can also find data related to samples physically maintained in other facilities, directly associated with NEON or not.</p>
    <p>Whenever possible, records are associated with taxa.</p>
    <p>NEON Science describes their methods for determining taxa in their protocols.</p>
    <p>A complete, searchable list for NEON Science taxonomic entities and references is available here: <a href="https://data.neonscience.org/apps/taxon">https://data.neonscience.org/apps/taxon</a>.</p>
    <p>Because the Biorepository Data Portal is a Symbiota portal, taxa have to be placed in a taxonomic thesaurus.</p>
    <p>In our data workflows, scientific names are checked for quality with several taxonomic authorities relevant to each domain. Therefore, a particular taxon might have been: individually checked and determined by a specialist; or programmatically checked against taxonomic APIs.</p>
    <p>Following the references for each taxon one will be able to find which taxonomic classification is being used for that particular taxon.</p>
    <p>Below are available source lists used in our database. External, NON-NEON collections (seinet, cvscoll, etc) were not contemplated by this brief analysis.</p>
    <div>
      <h2>Taxonomic sources by collection (in database)</h2>
      <p>Below is a non-exaustive list of taxonomic sources used to match agains our data, by taxonomic category/collection (alphabetically sorted by category).</p>

      <?php 
        $sourceArr = $sources->getOccSourcesByColl();
        $headerArr = ['Category', 'Collection', 'Taxonomic Source'];

        if($sourceArr){
          $sourceTable = $sources->htmlTable($sourceArr, $headerArr);
          echo $sourceTable;
        } else {
          $this_>logOrEcho($sources->errorMessage, 0, 'div');
        }
      ;?>
    </div>
    
    <div>
      <h2>List of taxonomic sources (in database)</h2>
      <p>Below is a full list of unique taxonomic sources used in our collections, in alphabetical order.</p>
      <?php 
        $sourceListArr = $sources->getUniqueOccSources();
        if($sourceListArr){
          echo '<ul>';
          foreach ($sourceListArr as $item) {
            echo '<li>'. $item . '</li>';
          }
          echo '</ul>';
        } else {
          $this->logOrEcho($sources->errorMessage, 0 , 'div');
        }
      ;?>
    </div>

    <!-- <div>
      <h2>List of official NEON taxonomic sources by taxonomic category, in use in our database</h2>

    </div> -->

    </div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
  </body>
</html>