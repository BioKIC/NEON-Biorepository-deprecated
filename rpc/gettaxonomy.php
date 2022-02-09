<?php
/*
 * Input: string representing scientific name
 * Return: JSON containing scientific name and taxonomic hierarchy
 * 
 */
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/APITaxonomy.php');
header('Content-Type: application/json');

$taxonAPI = new APITaxonomy();
$taxonArr = $taxonAPI->getTaxonomy($_REQUEST["sciname"]);

echo json_encode($taxonArr, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>