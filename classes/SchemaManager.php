<?php
use function PHPUnit\Framework\fileExists;

include_once($SERVER_ROOT.'/classes/Manager.php');

class SchemaManager extends Manager{

	private $adminConn;
	private $currentVersion;
	private $verionDate;
	private $versionHistory = array();
	private $activeTableArr;

	public function __construct(){
		parent::__construct();
	}

	public function __destruct(){
		parent::__destruct();
	}

	public function installPatch($host, $username, $password, $database, $port, $schemaPatch){
		$this->setVerboseMode(3);
		$this->setLogFH($GLOBALS['SERVER_ROOT'] . '/content/logs/install/db_schema_patch-' . $schemaPatch. '_'.date('Y-m-d').'.log');
		if($this->setDatabaseConnection($host, $username, $password, $database, $port)){
			$this->logOrEcho('Connection to database established');
			if($sqlArr = $this->readSchemaFile($schemaPatch)){
				$this->logOrEcho('DB schema patch file read: '. count($sqlArr) . ' statements to apply');
				foreach($sqlArr as $cnt => $stmtArr){
					$this->logOrEcho('Statement #' . $cnt . ' (' . date("Y-m-d H:i:s") . ')');
					$stmtType = '';
					$targetTable = '';
					$sql = '';
					foreach($stmtArr as $fragment){
						if(substr($fragment, 0, 1) == '#'){
							//is comment
							trim($fragment, '#');
							$this->logOrEcho($fragment, 1);
						}
						elseif(!$stmtType){
							if(preg_match('/`([a-z]+)`/', $fragment, $m)){
								$targetTable = $m[1];
							}
							$stmtType = 'undefined';
							if(strpos($fragment, 'schemaversion') !== false) $stmtType = 'schemaversion';
							elseif(strpos($fragment, 'CREATE TABLE') === 0) $stmtType = 'CREATE TABLE';
							elseif(strpos($fragment, 'ALTER TABLE') === 0){
								$stmtType = 'ALTER TABLE';
								$this->setActiveTable($targetTable);
							}
							elseif(strpos($fragment, 'INSERT') === 0) $stmtType = 'INSERT';
							$this->logOrEcho('Statement type: ' . $stmtType, 1);
							if($targetTable) $this->logOrEcho('Target table: ' . $targetTable, 1);
						}
						else{
							if($stmtType == 'ALTER TABLE') $fragment = $this->verifyAlterTableFragment($fragment);
							$sql .= ' ' . $fragment;
						}
					}
					if($sql){
						$this->logOrEcho('Statement: ' . $sql, 1);
						if(!$this->conn->query($sql)){
							$this->logOrEcho('ERROR applying statement: ' . $this->conn->error, 1);

						}
					}
				}
			}
			else{
				$this->errorMessage = 'ERROR reading schema patch file';
			}
		}
	}

	private function readSchemaFile($schemaPatch){
		$sqlArr = false;
		$filename = $GLOBALS['SERVER_ROOT'] . '/config/schema-1.0/utf8/db_schema_patch-' . $schemaPatch . '.sql';
		if(file_exists($filename)){
			$fileHandler = @fopen($filename, 'r');
			if ($fileHandler) {
				$sqlArr = array();
				$cnt = 0;
				while ($line = fgets($fileHandler)) {
					$line = trim($line);
					if($line){
						$sqlArr[$cnt][] = $line;
					}
					else{
						$cnt++;
					}
				}
				fclose($fileHandler);
			}
		}
		else{
			$this->errorMessage = 'ABORT: db schema patch does not exist: ' . $filename;
			return false;
		}
		return $sqlArr;
	}

	private function setActiveTable($targetTable){
		unset($this->activeTableArr);
		if($targetTable){
			$this->activeTableArr = array();
			$sql = 'SHOW COLUMNS FROM ' . $targetTable;
			$rs = $this->conn->query($sql);
			while($r = $rs->fetch_object()){
				$fieldName = strtolower($r->Field);
				$type = $r->Type;
				if(preg_match('/^[a-z]+/', $type, $m)){
					$this->activeTableArr[$fieldName]['type'] = $m[1];
				}
				if(preg_match('#\(([\d]*?)\)#', $type, $n)){
					$this->activeTableArr[$fieldName]['length'] = $n[1];
				}
			}
			$rs->free();
		}
	}

	private function verifyAlterTableFragment($fragment){
		if($this->activeTableArr){
			if(strpos($fragment, 'ADD COLUMN') !== false){
				if(preg_match('/^ADD COLUMN `([A-Za-z]+)`/', $fragment, $m)){
					$colName = strtolower($m[1]);
					if(!array_key_exists($colName, $this->activeTableArr)) return false;

				}
			}
			elseif(strpos($fragment, 'CHANGE COLUMN') !== false){
				if(preg_match('/^CHANGE COLUMN `([A-Za-z]+)` .+ VARCHAR\((\d+)\)/', $fragment, $m)){
					$colName = strtolower($m[1]);
					if(!array_key_exists($colName, $this->activeTableArr)) return false;
					$colWidth = $m[1];
					if(isset($this->activeTableArr[$colName]['length']) && $colWidth < $this->activeTableArr[$colName]['length']){
						$fragment = preg_match_replace('/VARCHAR\((\d+)\)/', 'VARCHAR(' . $this->activeTableArr[$colName]['length'] . ')', $fragment);
					}
				}
			}
		}
		return $fragment;
	}

	//Misc support functions
	private function setDatabaseConnection($host, $username, $password, $database, $port){
		if($host && $username && $password && $database && $port){
			$this->conn = new mysqli($host, $username, $password, $database, $port);
			if($this->conn->connect_error){
				$this->errorMessage = 'Connection error: ' . $this->conn->connect_error;
				return false;
			}
		}
		else{
			$this->conn = MySQLiConnectionFactory::getCon('admin');
		}
		return true;
	}

	//Misc data retrival functions
	public function setVerionHistory(){
		$sql = 'SELECT versionNumber, dateApplied FROM schemaversion ORDER BY id';
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$this->versionHistory[$r->versionNumber] = $r->dateApplied;
			$this->currentVersion = $r->versionNumber;
			$this->versionDate = $r->dateApplied;
		}
		$rs->free();
	}

	//Setters and getters
	public function getCurrentVersion(){
		return $this->currentVersion;
	}

	public function getVersionDate(){
		return $this->versionDate;
	}

	public function getVersionHistory(){
		if(!$this->versionHistory) $this->setVerionHistory();
		return $this->versionHistory;
	}
}
?>