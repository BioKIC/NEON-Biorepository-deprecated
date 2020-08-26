<?php 

  include_once('../../config/symbini.php');
  include_once('../../config/dbconnection.php');

  $conn = MySQLiConnectionFactory::getCon('readonly');
  $id = ((array_key_exists("collid",$_REQUEST) && is_numeric($_REQUEST["collid"]))?$_REQUEST["collid"]:0);
  $sql = 'SELECT collid, institutioncode, collectionname, datasetname, fulldescription, homepage, dwcaurl FROM omcollections WHERE collid='.$id.'';
  $sql = $conn->real_escape_string($sql);
  $result = $conn->query($sql);
  $dataArr = array();

  if ($id && $result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()){
      $dataArr[] = $row;
    }
    echo json_encode($dataArr);
    }
    else {
      http_response_code(403);
    }
   $conn->close();
?>