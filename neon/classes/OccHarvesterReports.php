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

  // Formats shipment urls (dependes heavily on specific query)
  public function getShipmentUrl($pkCol, $idCol){
    $pksArr = explode(';', $pkCol);
    $idsArr = explode(';', $idCol);

    $urlList = '';
    foreach ($pksArr as $pk){
      $urlList = $urlList.'<a href="manifestviewer.php?shipmentPK='.$pk.'">'.$idsArr[(array_search($pk, $pksArr))].'</a></br>';
    }
    return $urlList;
  }

  // Gets data about harvesting errors grouped by sampleClass
  public function getHarvestReport(){
    $dataArr = array();

    $sql = 'SELECT IF(collid IS NULL, " ", collid) AS collid, sampleClass, errorMessage, count(*) AS cnt, GROUP_CONCAT(DISTINCT s.shipmentPK SEPARATOR ";") AS shipmentPK, GROUP_CONCAT(DISTINCT sh.shipmentID SEPARATOR ";") AS shipmentID FROM NeonSample s  LEFT JOIN omoccurrences o ON s.occid = o.occid JOIN NeonShipment sh ON s.shipmentPK = sh.shipmentPK WHERE errorMessage IS NOT NULL GROUP BY errorMessage, sampleClass, collid;';

    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
      //output data of each row
      while ($row = $result->fetch_assoc()){
        // originally
        // $dataArr[] = $row;
        $dataArr[] = array(
          $row['collid'],  
          $row['sampleClass'],  
          $row['errorMessage'],  
          $row['cnt'],
          $this->getShipmentUrl($row['shipmentPK'], $row['shipmentID']),
        );
      }
      $result->free();
    } 
    else {
      $this->errorMessage = 'Harvest report query was not successfull';
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
          //  original
            $cells[] = "<td>{$cell}</td>";
        }
        $rows[] = "<tr>" . implode('', $cells) . "</tr>";
    }
    return '<table class="table-sortable"><thead>'. implode('', array_merge($headers)).'</thead>' . implode('', array_merge($rows)) . "</table>";
  }
}

;?>