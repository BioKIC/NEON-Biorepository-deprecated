<?php
include_once($SERVER_ROOT.'/classes/Manager.php');

class RpcBase extends Manager{


	function __construct($conType='readonly'){
		parent::__construct(null,$conType);
	}

	function __destruct(){
		parent::__destruct();
	}

	protected function isValidApiCall(){
		//First of all check request is AJAX request or not.
		//if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') return false;

		//Check Referer: only valid when AJAX request is local
		//if(empty($_SERVER['HTTP_REFERER']) || basename($_SERVER['HTTP_REFERER']) == basename($_SERVER['PHP_SELF'])) return false;

		//TODO: Maybe add increased security with a session token included
		return true;
	}
}
?>