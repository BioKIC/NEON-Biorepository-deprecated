<?php
include_once($SERVER_ROOT.'/classes/RpcBase.php');

class RpcUsers extends RpcBase{

	function __construct(){
		parent::__construct();
	}

	function __destruct(){
		parent::__destruct();
	}

	public function getUserArr($term){
		$retArr = array();
		$sql = 'SELECT u.uid, CONCAT(CONCAT_WS(", ", u.lastname, u.firstname)," (",l.username,")") as uname
			FROM users u INNER JOIN userlogin l ON u.uid = l.uid
			WHERE u.lastname LIKE "%'.$term.'%" OR u.firstname LIKE "%'.$term.'%" OR l.username LIKE "%'.$term.'%"
			ORDER BY u.lastname, u.firstname, l.username';
		$rs = $this->conn->query($sql);
		$cnt = 0;
		while($r = $rs->fetch_object()){
			$retArr[$cnt]['id'] = $r->uid;
			$retArr[$cnt]['label'] = $r->uname;
			$cnt++;
		}
		$rs->free();
		return $retArr;
	}

	public function isValidApiCall(){
		//Verification also happening within haddler checking is user is logged in and a valid admin/editor
		$status = parent::isValidApiCall();
		if(!$status) return false;
		return true;
	}
}
?>