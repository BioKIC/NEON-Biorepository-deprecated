<?php 

  include_once($SERVER_ROOT.'/classes/Manager.php');

 /**
 * Controler class for /neon/classes/OccHarvesterReports.php
 * 
 */

 class OccHarvesterReports extends Manager {
   
  public function __construct() {
    parent::__construct(null,'readonly');
    $this->verboseMode = 2;
    set_time_limit(2000);
  }

  public function __destruct() {
    parent::__destruct();
  }

  // Main functions

  // Gets data about harvesting errors grouped by sampleClass
  public function getHarvestReport(){
    $dataArr = array();

    $sql = 'SELECT collid, sampleClass, errorMessage, count(*) as cnt FROM NeonSample s LEFT JOIN omoccurrences o ON s.occid = o.occid WHERE errorMessage IS NOT NULL GROUP BY errorMessage, sampleClass, collid;';

    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
      //output data of each row
      while ($row = $result->fetch_assoc()){
        $dataArr[] = $row;
      }
      $result->free();
    } 
    else {
      $this->errorMessage = 'Harvest report query was not successfull';
      $dataArr = false;
    }
    return $dataArr;
  }
}

;?>