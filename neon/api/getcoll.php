<?php 
  include_once('../../config/symbini.php');
  include_once($SERVER_ROOT.'/neon/classes/CollectionMetadata.php');
  header("Content-Type: text/html; charset=".$CHARSET);

  // collid -> get from http request and assign to variable
  $id = (array_key_exists("collid",$_REQUEST)?$_REQUEST["collid"]:'');

  if ($id) {
    $coll = new CollectionMetadata();
    // pass variable to function below
    $collInfo = $coll->getCollMetaById($id);
  
    if (!$collInfo) {
      echo json_encode([]);
    } 
    else {
      echo json_encode($collInfo);
    }
  }
  else {
    echo 'Add the collection id parameter to your request';
  }
  
?>