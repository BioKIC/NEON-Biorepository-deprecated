<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/neon/classes/Sources.php');
header("Content-Type: text/html; charset=".$CHARSET);

$taxa = new Sources();

$taxaArr = $taxa->getTaxaWithSources(10);
$taxaCsv = $taxa->getTaxaWithSources(1000);
$headerArr = ['Collection Category', 'NEON Taxon Type Code', 'collid', 'NEON Taxon ID', 'sciname', 'Source in Symbiota', 'Source in NEON API'];

  if(!empty($taxaArr)){
    $neonArr = array();
    foreach ($taxaArr as &$row) {
      $sciname = $row['sciname'];
      $neonSource = $taxa->getNeonSourcesFromAPI($sciname);
      $row['neonsourcefromapi'] = $neonSource;
      // echo "$sciname - $neonSource <br>";
    };
    $taxaTable = $taxa->htmlTable($taxaArr, $headerArr);
    $taxa->downloadTaxSources($taxaArr,$headerArr,'biorepo-taxa-with-sources.csv');
    };
?>