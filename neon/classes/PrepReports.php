<?php

  include_once($SERVER_ROOT.'/classes/Manager.php');

 /**
 * Controler class for /neon/classes/PrepReports.php
 *
 */

 class PrepReports extends Manager {

  public function __construct() {
    parent::__construct(null,'readonly');
    $this->verboseMode = 2;
    set_time_limit(2000);
  }

  public function __destruct() {
    parent::__destruct();
  }

  // Main functions

  // Gets data about preparations grouping by preparators
  // Uses "preparedBy" key:value available in "dynamicProperties" in table "omoccurrences"
  // For NEON, specifically used for Mammal collections, counting skin and ethanol preparations
  public function getMamPrepsCntByPreparator(){
    $dataArr = array();

    $sql = 'SELECT ethanols.prepBy, IFNULL(skinPrepCnt,0) AS skinPrepCnt, IFNULL(fluidPrepCnt, 0) AS fluidPrepCnt, IFNULL(skinPrepCnt,0) + IFNULL(fluidPrepCnt, 0) AS total
FROM (SELECT TRIM(REGEXP_SUBSTR(dynamicProperties,"(?<=preparedBy:)(.*?)(?=,)")) AS prepBy, COUNT(occid) AS skinPrepCnt FROM omoccurrences WHERE dynamicProperties LIKE "%preparedBy%" AND preparations LIKE "%skin%" AND collid IN (17,19,24,25,26,27,28,64,71) GROUP BY prepBy) AS skins RIGHT JOIN ( SELECT TRIM(REGEXP_SUBSTR(dynamicProperties,"(?<=preparedBy:)(.*?)(?=,)")) AS prepBy, COUNT(occid) AS fluidPrepCnt FROM omoccurrences WHERE dynamicProperties LIKE "%preparedBy%" AND preparations LIKE "%eth%" AND collid IN (17,19,24,25,26,27,28,64,71) GROUP BY prepBy ) AS ethanols ON skins.prepBy = ethanols.prepBy ORDER BY prepBy;';

    $result = $this->conn->query($sql);

    if ($result) {
      //output data of each row
      while ($row = $result->fetch_assoc()){
        // originally
        // $dataArr[] = $row;
        $dataArr[] = array(
          $row['prepBy'],
          $row['skinPrepCnt'],
          $row['fluidPrepCnt'],
          $row['total'],
        );
      }
      $result->free();
    }
    else {
      $this->errorMessage = 'Preparations report query was not successfull';
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
?>