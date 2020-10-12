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

  // Gets a list of taxa with Symbiota sources and NEON codes
  public function getTaxaWithSources($limit){
    $dataArr = array();

    $sql = 'SELECT * FROM taxsources LIMIT '.$limit;

    $result = $this->conn->query($sql);

    if ($result->num_rows > 0){
      //output data of each row
      while($row = $result->fetch_assoc()){
        $dataArr[] = $row;
      }
      $result->free();
    }
    else {
      $this->errorMessage = 'Taxa with sources query was not successfull';
      $dataArr = false;
    }
    return $dataArr;
  }

  // Gets taxon reference from NEON API
  // Needs to handle multiple responses... currently just gets first one
  public function getNeonSourcesFromAPI($sciname){
    $apiUrl = 'https://data.neonscience.org/api/v0/taxonomy?scientificname='.urlencode($sciname);
    
    if($resJson = file_get_contents($apiUrl)){
      $resArr = json_decode($resJson, true);
      $source = $resArr['data'][0]['dwc:nameAccordingToID'];
    }
    else {
      $source = 'error';
    };
    return $source;
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

  // Generates CSV file to download
  public function downloadTaxSources($data, $header, $fileName){
    header ('Content-Type: text/csv');
    header ("Content-Disposition: attachment; filename=\"$fileName\"");
    if($data){
      $outstream = fopen("php://output", "w");
      fputcsv($outstream,$header);

      foreach($data as $row){
        fputcsv($outstream,$row);
      }
      fclose($outstream);
    }
    else{
      echo "Recordset is empty.\n";
    }
  }
}
?>