<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/RpcTaxonomy.php');
header("Content-Type: application/json; charset=".$CHARSET);

$objId = array_key_exists('id',$_REQUEST)?$_REQUEST['id']:0;
$targetId = array_key_exists('targetid',$_REQUEST)?$_REQUEST['targetid']:0;
$taxAuthId = array_key_exists('taxauthid',$_REQUEST)?$_REQUEST['taxauthid']:1;
$editorMode = array_key_exists('emode',$_REQUEST)?$_REQUEST['emode']:true;
$displayAuthor = array_key_exists('authors',$_REQUEST)?$_REQUEST['authors']:0;

$rpcManager = new RpcTaxonomy();
$rpcManager->setTaxAuthId($taxAuthId);

$retArr = $rpcManager->getDynamicChildren($objId, $targetId, $displayAuthor, $editorMode);
echo json_encode($retArr);
?>