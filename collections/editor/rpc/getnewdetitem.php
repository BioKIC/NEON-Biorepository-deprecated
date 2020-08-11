<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceEditorDeterminations.php');

$collid = array_key_exists('collid',$_POST)?$_POST['collid']:'';
$catNum = array_key_exists('catalognumber',$_POST)?$_POST['catalognumber']:'';
$allCatNum = array_key_exists('allcatnum',$_POST)?$_POST['allcatnum']:0;
$sciName = array_key_exists('sciname',$_POST)?$_POST['sciname']:'';

$retArr = array();
if(is_numeric($collid)){
	$occManager = new OccurrenceEditorDeterminations();
	$occManager->setCollId($collid);
	$retArr = $occManager->getNewDetItem($catNum,$sciName,$allCatNum);
}

echo json_encode($retArr);
?>