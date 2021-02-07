<?php
	include_once('../../../config/symbini.php');
	include_once($SERVER_ROOT.'/classes/ExsiccatiManager.php');

	$exsManager = new ExsiccatiManager();
	$exsArr = $exsManager->getExsiccatiSuggest($_REQUEST['term']);

	echo json_encode($exsArr);
?>