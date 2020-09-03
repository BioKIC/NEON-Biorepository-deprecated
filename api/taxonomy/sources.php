<?php 

  include_once('../../config/symbini.php');
  include_once('../../config/dbconnection.php');
  include_once($SERVER_ROOT.'/neon/classes/Sources.php');

  $filter = (array_key_exists("filter",$_REQUEST)?$_REQUEST["filter"]:'');
  $sources = new Sources();

  switch ($filter) {
    case "coll":
      $sourceArr = $sources->getOccSourcesByColl();
    break;
    case "unique":
      $sourceArr = $sources->getUniqueOccSources();
    break;
  }

  if ($sourceArr === false) {
    http_response_code(403);
  } else {
    echo json_encode($sourceArr);
  }

?>