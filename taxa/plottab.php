<?php
  include_once('../config/symbini.php');
  include_once($SERVER_ROOT.'/content/lang/taxa/index.'.$LANG_TAG.'.php');
  include_once($SERVER_ROOT.'/classes/TaxonProfile.php');
  include_once($SERVER_ROOT.'/classes/TraitPlotManager.php');
  include_once($SERVER_ROOT.'/classes/TraitPolarPlot.php');
  Header('Content-Type: text/html; charset='.$CHARSET);

  $tid = $_REQUEST['tid'];
  $taxAuthId = array_key_exists('taxauthid',$_REQUEST)?$_REQUEST['taxauthid']:1;
  $traitid = 1; //get this from $GLOBAL array_key_exists("traitid",$_REQUEST)?$_REQUEST["traitid"]:""

  //Sanitation
  if(!is_numeric($tid)) $tid = 0;
  if(!is_numeric($traitid)) $traitid = 0;

  $taxonManager = new TaxonProfile();
  $taxonManager->setTid($tid);

  $traitPlotter = new TraitPlotManager("polar");
  if($tid) $traitPlotter->setTid($tid);
  if($traitid) $traitPlotter->setTraitid($traitid);
?>

<div id="tab-calendarplot" class="sptab">
	<?php
  	echo '<div class="resource-title">Trait name and state</div>';
	?>
</div>
