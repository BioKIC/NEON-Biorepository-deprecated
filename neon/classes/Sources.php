<?php 
include_once($SERVER_ROOT.'/classes/Manager.php');

/**
 * Controler class for /neon/classes/Search.php
 * 
 */

class Sources extends Manager { 

	// private $variable1;
	// private $variable2;
	
	public function __construct(){
    parent::__construct(null,'readonly');
    $this->verboseMode = 2;
		set_time_limit(2000);
	}

	public function __destruct(){
 		parent::__destruct();
	}
	
  //Main functions
  
  // Gets a list of sources from occurrences by collection
  public function getOccSourcesByColl(){
    $dataArr = array();

    $sql = 'SELECT category, collectionname, source FROM taxsourcescoll ORDER BY category, collectionname';

    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()){
      $dataArr[] = $row;
    }
    $result->free();
    }
    else {
      $this->errorMessage = 'Sources by Coll query was not successfull';
      $dataArr = false;
    }
    return $dataArr;
  }

  // Gets a list of unique sources from occurrences
  public function getUniqueOccSources(){
    $dataArr = array();

    $sql = 'SELECT DISTINCT source FROM taxsourcescoll ORDER BY source';

    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()){
      $dataArr[] = $row["source"];
    }
    $result->free();
    return $dataArr;
    }
    else {
      $this->errorMessage = 'Sources by Coll query was not successfull';
    }
  }

  // Gets a list of NEON sources from occurrences by collection
  public function getNeonSourcesByColl(){
    $dataArr = array();

    $sql = 'SELECT taxoncategory, source FROM neontaxsources ORDER BY taxoncategory, source';

    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()){
      $dataArr[] = $row;
    }
    $result->free();
    }
    else {
      $this->errorMessage = 'NEON Sources by Coll query was not successfull';
      $dataArr = false;
    }
    return $dataArr;
  }
	
  // Formats array in tabular form (pass array name and headers array as arguments)
  public function htmlTable($data, $headerArr){
    foreach ($headerArr as $header){
      $headers[] = "<th>{$header}</th>";
    }
    $rows = array();
    foreach ($data as $row) {
        $cells = array();
        foreach ($row as $cell) {
            $cells[] = "<td>{$cell}</td>";
        }
        $rows[] = "<tr>" . implode('', $cells) . "</tr>";
    }
    return "<table>" . implode('', array_merge($headers, $rows)) . "</table>";
  }
	
} 
?>