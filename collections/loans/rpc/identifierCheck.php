<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/SpecLoans.php');

$collid = $_REQUEST['collid'];
$id = $_REQUEST['ident'];
$type = $_REQUEST['type'];

$loanManager = new SpecLoans();
$loanManager->setCollId($collid);
$retMsg = $loanManager->identifierExists($id,$type);

echo $retMsg;
?>