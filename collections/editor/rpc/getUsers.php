<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/RpcUsers.php');
header("Content-Type: application/json; charset=".$CHARSET);

$term = $_POST['term'];
$collid = isset($_POST['collid'])?$_POST['collid']:0;

$rpcManager = new RpcUsers();
$retArr = $rpcManager->getUserArr($term);

echo json_encode($retArr);
?>