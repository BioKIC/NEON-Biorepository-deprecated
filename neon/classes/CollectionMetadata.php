<?php 

  include_once($SERVER_ROOT.'/classes/Manager.php');

 /**
 * Controler class for /neon/classes/CollectionMetadata.php
 * 
 */

 class CollectionMetadata extends Manager {
   
  public function __construct() {
    parent::__construct(null,'readonly');
    $this->verboseMode = 2;
    set_time_limit(2000);
  }

  public function __destruct() {
    parent::__destruct();
  }

  // Main functions

  // Gets individual collection profile metadata with collid
  public function getCollMetaById($collid){
    $dataArr = array();

    $sql = 'SELECT * FROM collmetadata WHERE collid = '.$collid.'';

    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
      //output data of each row
      while ($row = $result->fetch_assoc()){
        $dataArr[] = $row;
      }
      $result->free();
    } 
    else {
      $this->errorMessage = 'Collection Metadata query was not successfull';
      $dataArr = false;
    }
    return $dataArr;
  }
}

;?>