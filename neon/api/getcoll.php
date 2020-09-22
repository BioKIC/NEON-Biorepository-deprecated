<?php 
  include_once('../../config/symbini.php');
  include_once($SERVER_ROOT.'/neon/classes/CollectionMetadata.php');
  header("Content-Type: text/html; charset=".$CHARSET);

  /*
  * Collection Metadata API endpoint
  * Parameter: collection id (collid)
  * Returns: JSON-encoded array containing metadata 
  * for a particular collection record. If no results 
  * are found, returns empty array.
  */

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