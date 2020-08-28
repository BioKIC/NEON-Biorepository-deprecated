<?php 

  include_once('../../config/symbini.php');
  include_once('../../config/dbconnection.php');

  $conn = MySQLiConnectionFactory::getCon('readonly');
  $filter = (array_key_exists("filter",$_REQUEST)?$_REQUEST["filter"]:'');

  switch ($filter) {
    case "coll":
      $sql = 'SELECT category, collectionname, collid, source FROM taxsourcescoll ORDER BY category';
    break;
    case "unique":
      $sql = 'SELECT DISTINCT source FROM taxsourcescoll ORDER BY source';
    break;
  }

  $sql = $conn->real_escape_string($sql);
  $result = $conn->query($sql);
  $dataArr = array();

  if ($result->num_rows > 0) {
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