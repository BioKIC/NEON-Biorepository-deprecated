<?php
include_once($SERVER_ROOT.'/classes/RpcBase.php');

class RpcUsers extends RpcBase{

	private $collid = 0;

	function __construct(){
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}

	public

	public function isCallValid(){
		$status = parent::isCallValid();
		if(!$status) return false;
		if($GLOBALS['IS_ADMIN']) return true;
		$userRights = $GLOBALS['USER_RIGHTS'];
		if(!$this->collid || !$userRights) return false;
		if(array_key_exists('CollEditor',$userRights) && in_array($this->collid,$userRights['CollEditor'])) return true;
		if(array_key_exists('CollAdmin',$userRights) && in_array($this->collid,$userRights['CollAdmin'])) return true;
		return false;
	}

	//Setters and getters
	public function setCollid($id){
		if(is_numeric($id)) $this->collid = $id;
	}
}
?>