<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
$retMsg = 0;

$collid = $_REQUEST['collid'];
$loanid = $_REQUEST['loanid'];
$catalogNumber = $_REQUEST['catalognumber'];


if($loanid && $collid && $catalogNumber){
	if($IS_ADMIN
	|| ((array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))
	|| (array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollEditor'])))){
		$loanManager = new OccurrenceLoans();
		$loanManager->setCollId($collid);
		$retMsg = $loanManager->linkSpecimen($loanid,$catalogNumber);
	}
}
echo $retMsg;
?>