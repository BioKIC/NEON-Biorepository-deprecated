<?php 

  include_once($SERVER_ROOT.'/classes/Manager.php');

 /**
 * Controler class for /neon/classes/OccurrenceQuickSearch.php
 * 
 */

 class OccurrenceQuickSearch extends Manager {
   
  public function __construct() {
    parent::__construct(null,'readonly');
    $this->verboseMode = 2;
    set_time_limit(2000);
  }

  public function __destruct() {
    parent::__destruct();
  }

  // Searches occurrences by taxon in collection, to use in taxon profile
  public function getOccTaxonInDbCnt($tid, $collids){
    $dataArr = array();
    $collidsStr = implode(",",$collids);
    // $sql = 'SELECT DISTINCT col.collid FROM omcollections AS col LEFT JOIN omcollcatlink AS l ON col.CollID = l.collid LEFT JOIN omcollcategories AS cat ON l.ccpk = cat.ccpk WHERE l.ccpk NOT IN (6,8) AND available = "TRUE"';

    $sql = 'SELECT COUNT(occid) FROM omoccurrences WHERE collid IN ('.$collidsStr.') AND tidinterpreted = '.$tid.'';
  
    $result = $this->conn->query($sql);

    // while ($row = $result->fetch_assoc()){
    //   $dataArr[] = $row['collid'];
    // }
    // $result->free(); 

    while ($row = $result->fetch_row()){
      $count = $row;
    }
    $result->free(); 
    return $count[0];
    
    // $dataStr = implode(",", $dataArr);
    // return $dataStr;
    // return $collidsStr; 
    // return $tid;
  }
};

;?>