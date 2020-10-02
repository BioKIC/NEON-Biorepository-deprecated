<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/Sources.php');
header("Content-Type: text/html; charset=".$CHARSET);

$taxa = new Sources();

$taxaArr = $taxa->getTaxaWithSources();
$headerArr = ['Collection Category', 'NEON Taxon Type Code', 'collid', 'NEON Taxon ID', 'sciname', 'Source in Symbiota', 'Source in NEON API'];
?>
<html>
	<head>
		<title>Taxa With Sources</title>
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
			<b>Taxa with Sources</b>
		</div>
		<!-- This is inner text! -->
		<div id="innertext">
      <?php 
        if(!empty($taxaArr)){
          $neonArr = array();
          foreach ($taxaArr as &$row) {
            $sciname = $row['sciname'];
            $neonSource = $taxa->getNeonSourcesFromAPI($sciname);
            $row['neonsourcefromapi'] = $neonSource;
            // echo "$sciname - $neonSource <br>";
          };
          $taxaTable = $taxa->htmlTable($taxaArr, $headerArr);
          echo $taxaTable;
          };
          ?>
		</div>
		<?php
			include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>
