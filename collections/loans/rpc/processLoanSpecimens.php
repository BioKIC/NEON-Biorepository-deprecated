<?php
include_once('../../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceLoans.php');
$retMsg = 0;

$collid = $_POST['collid'];
$loanid = $_POST['loanid'];
$catalogNumber = $_POST['catalognumber'];
$processMode = $_POST['processmode'];
$target = $_POST['target'];


if($loanid && $collid && $catalogNumber){
	if($IS_ADMIN
	|| ((array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin']))
	|| (array_key_exists('CollEditor',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollEditor'])))){
		$loanManager = new OccurrenceLoans();
<<<<<<< HEAD:collections/loans/rpc/insertLoanSpecimens.php
		//$loanManager->setCollId($collid);
		$retMsg = $loanManager->linkSpecimen($loanid,$catalogNumber);
=======
		$loanManager->setCollId($collid);
		if($processMode == 'link') $retMsg = $loanManager->linkSpecimen($loanid,$catalogNumber,$target);
		elseif($processMode == 'checkin') $retMsg = $loanManager->checkinSpecimen($loanid, $catalogNumber, $target);
>>>>>>> 0b5ded2f3feb7b5375ef7b1d0f7685df54645486:collections/loans/rpc/processLoanSpecimens.php
	}
}
echo $retMsg;
?>