<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/RpcTaxonomy.php');
header("Content-Type: application/json; charset=".$CHARSET);

$tid = $_REQUEST['tid'];
$taxAuthId = array_key_exists('taxauthid',$_REQUEST)?$con->real_escape_string($_REQUEST['taxauthid']):'1';

$rpcManager = new RpcTaxonomy();
$rpcManager->setTaxAuthId($taxAuthId);
$retArr = $rpcManager->getChildAccepted($tid);

echo json_encode($retArr);
?>