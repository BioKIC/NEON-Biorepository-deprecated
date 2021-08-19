<?php


if($isEditor){
	if($action == 'saveMatSample'){
		include_once($SERVER_ROOT.'/classes/OccurrenceEditorMaterialSample.php');
		$materialSampleManager = new OccurrenceEditorMaterialSample();
		$materialSampleManager->setOccid($occid);
		$matSampleID = isset($_REQUEST['matSampleID'])?$_REQUEST['matSampleID']:'';
		if(!is_numeric($matSampleID)) $matSampleID = 0;
		$materialSampleManager->setMatSampleID($matSampleID);

	}
}



?>