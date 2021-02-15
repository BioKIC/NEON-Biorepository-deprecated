<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/CollectionMetadata.php');
header("Content-Type: text/html; charset=".$CHARSET);

$data = new CollectionMetadata();

?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Collections Metadata Example</title>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET;?>" />
		<?php
		$activateJQuery = true;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
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
			<b>Collections Metadata Example</b>
		</div>
		<div id="innertext">
      <h2>Additional NEON Collections</h2>
      <?php if($collsArr = $data->getCollMetaByCat('Additional NEON Collections')){
        foreach($collsArr as $result) {
          // echo $result['category'].' ';
          echo $result['collid'].' ';
          echo $result['collectionname'].' ';
          // echo $resul['collectioncode'].' ';
          echo '<br>';
        }
      } ;?>
      <h2>Other Collections from NEON sites</h2>
      <?php if($collsArr = $data->getCollMetaByCat('Other Collections from NEON sites')){
        foreach($collsArr as $result) {
          // echo $result['category'].' ';
          echo $result['collid'].' ';
          echo $result['collectionname'].' ';
          // echo $resul['collectioncode'].' ';
          echo '<br>';
        }
      } ;?>
      <h2>All NEON Biorepository Collections</h2>
      <!-- Query subcategories with GROUP BY  -->
      <h3>By Taxonomic Group</h3>
      <?php if($groupsArr = $data->getBiorepoGroups('highertaxon')){
        foreach($groupsArr as $result) {
          if($result['highertaxon']){
            echo '<ul>'.$result['highertaxon'];
            $collsArr = $data->getBiorepoColls('highertaxon', $result['highertaxon']);
            if($collsArr){
              foreach($collsArr as $row) {
                echo '<li>';
                echo $row['collid'].' ';
                echo $row['collectionname'].' ';
                echo $row['available'].' ';
                echo '</li>';          
              }
            }
            echo '</ul>';
            echo '<br>';
          }
        }
      }
       ;?>
      <h3>By NEON Theme</h3>
      <?php if($groupsArr = $data->getBiorepoGroups('neontheme')){
        foreach($groupsArr as $result) {
          if($result['neontheme']){
            echo '<ul>'.$result['neontheme'];
            $collsArr = $data->getBiorepoColls('neontheme', $result['neontheme']);
            if($collsArr){
              foreach($collsArr as $row) {
                echo '<li>';
                echo $row['collid'].' ';
                echo $row['collectionname'].' ';
                echo $row['available'].' ';
                echo '</li>';          
              }
            }
            echo '</ul>';
            echo '<br>';
          }
        }
      }
       ;?>
      <h3>By Sample Type</h3>
      <?php if($groupsArr = $data->getBiorepoGroups('sampletype')){
        foreach($groupsArr as $result) {
          if($result['sampletype']){
            echo '<ul>'.$result['sampletype'];
            $collsArr = $data->getBiorepoColls('sampletype', $result['sampletype']);
            if($collsArr){
              foreach($collsArr as $row) {
                echo '<li>';
                echo $row['collid'].' ';
                echo $row['collectionname'].' ';
                echo $row['available'].' ';
                echo '</li>';          
              }
            }
            echo '</ul>';
            echo '<br>';
          }
        }
      }
       ;?>
      </div>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
  </body>
</html>