<?php
class ImageDropbox extends Manager {

	private $collid;
	private $collMetaArr;
	private $dropboxApiUrl = 'https://api.dropboxapi.com/2/files/list_folder';
	private $authKey;
	private $basePath = '/Images/Global/';

	public function __construct($connType = 'write'){
		parent::__construct(null,$connType);
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function processImages(){
		if($this->collMetaArr){
			$collPath = $this->basePath.$this->collMetaArr['instcode'];
			if($this->collMetaArr['collcode']) $collPath .= '-'.$this->collMetaArr['collcode'];
			$fileListObj = json_encode($this->getFileList(getFileList($targetPath)));

		}
	}

	//Data functions
	public function setCollMeta(){
		if($this->collid){
			$sql = 'SELECT institutioncode, collectioncode, collectionname FROM omcollections WHERE collid = '.$this->collid;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$this->collMetaArr['instcode'] = $r->institioncode;
				$this->collMetaArr['collcode'] = $r->collectioncode;
				$this->collMetaArr['collname'] = $r->collectionname;
			}
			$rs->free();
		}
	}

	//DropBox API calls
	private function getFileList($targetPath){
		$listJson = '';
		$ch = curl_init($this->dropboxApiUrl);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json','Authorization: Bearer'));
		$dataArr = array( 'path' => $targetPath );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($dataArr) );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		if(!$listJson = curl_exec($ch)){
			$this->errorMessage = 'ERROR getting file list: '.curl_error($ch);
			$this->logOrEcho($this->errorMessage);
		}
		curl_close($ch);
		return $listJson;
	}

	//Setters and getters
	public function setCollid($collid){
		if(is_numeric($collid)){
			$this->collid = $collid;
			$this->setCollMeta();
		}
	}
}