<?php
//error_reporting(E_ALL);
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/DynamicChecklistManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$lat = $_POST['lat'];
$lng = $_POST['lng'];
$radius = $_POST['radius'];
$radiusUnits = $_POST['radiusunits'];
$dynamicRadius = (isset($DYN_CHECKLIST_RADIUS)?$DYN_CHECKLIST_RADIUS:10);
$tid = $_POST['tid'];
$interface = $_POST['interface'];

//sanitation
if(!is_numeric($lat)) $lat = 0;
if(!is_numeric($lng)) $lng = 0;
if(!is_numeric($radius)) $radius = 0;
if($radiusUnits != 'mi') $radiusUnits == 'km';
if(!is_numeric($dynamicRadius)) $dynamicRadius = 10;
if(!is_numeric($tid)) $tid = 0;

$dynClManager = new DynamicChecklistManager();

if(is_numeric($radius)){
	$dynClid = $dynClManager->createChecklist($lat, $lng, $radius, $radiusUnits, $tid);
}
else{
	$dynClid = $dynClManager->createDynamicChecklist($lat, $lng, $dynamicRadius, $tid);
}

if($interface == "key"){
	header("Location: ".$CLIENT_ROOT."/ident/key.php?dynclid=".$dynClid."&taxon=All Species");
}
else{
	header("Location: ".$CLIENT_ROOT."/checklists/checklist.php?dynclid=".$dynClid);
}
ob_flush();
flush();
$dynClManager->removeOldChecklists();
?>