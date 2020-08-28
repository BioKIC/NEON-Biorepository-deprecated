<?php
  include_once('../config/symbini.php');
  header("Content-Type: text/html; charset=".$CHARSET);
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
      <table id="sources-by-coll">
        <tr>
          <th>Category</th>
          <th>Collection</th>
          <th>Source</th>
        </tr>
      </table>
    </div>
    
    <div>
      <h2>List of taxonomic sources (in database)</h2>
      <p>Below is a full list of unique taxonomic sources used in our collections, in alphabetical order.</p>
      <ul id="unique-sources"></ul>
    </div>

    <div>
      <h2>List of official NEON taxonomic sources by taxonomic category, in use in our database</h2>

    </div>

    </div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
  </body>
  <script>
    const sourcesByColl = document.getElementById('sources-by-coll');
    const uniqueSources = document.getElementById('unique-sources');

    function createTable() {

      fetch('../api/taxonomy/sources.php?filter=coll')
        .then(res => res.json())
        .then((items) => {
          for (var [key, value] of Object.entries(items)) {
            var row = document.createElement('tr');
            var cell1 = document.createElement('td');
            cell1.innerHTML = items[key].category;
            var cell2 = document.createElement('td');
            cell2.innerHTML = items[key].collectionname;
            var cell3 = document.createElement('td');
            cell3.innerHTML = items[key].source;
            row.appendChild(cell1);
            row.appendChild(cell2);
            row.appendChild(cell3);
            sourcesByColl.appendChild(row);
          }})
    }

    function createList() {

      fetch('../api/taxonomy/sources.php?filter=unique')
        .then(res => res.json())
        .then((items) => {
          for (var [key] of Object.entries(items)) {
            var row = document.createElement('li');
            row.innerHTML = items[key].source;
            uniqueSources.appendChild(row);
          }})
    }

    createTable();
    createList();

  </script>
</html>