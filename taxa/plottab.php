<?php
  include_once('../config/symbini.php');
  include_once($SERVER_ROOT.'/content/lang/taxa/index.'.$LANG_TAG.'.php');
  include_once($SERVER_ROOT.'/classes/TaxonProfile.php');
  include_once($SERVER_ROOT.'/classes/TraitPlotManager.php');
  include_once($SERVER_ROOT.'/classes/TraitPolarPlot.php');
  Header('Content-Type: text/html; charset='.$CHARSET);

  $tid = $_REQUEST['tid'];
  $taxAuthId = array_key_exists('taxauthid',$_REQUEST)?$_REQUEST['taxauthid']:1;
  if(isset($CALENDAR_TRAIT_PLOTS)) {
    $traitstateids = explode(",", $CALENDAR_TRAIT_PLOTS);
    $traitstateids = array_map('trim', $traitstateids);
  } else {
    $traitstateids = array("0");
  }

  //Sanitation
  if(!is_numeric($tid)) $tid = 0;
  if(!is_array($traitstateids)) $traitstateids = array(0);

?>

<div id="tab-calendarplot" class="sptab">
	<?php
// testcases: 31307, 18097, 8928
    foreach($traitstateids as $tsid) {
      if(!is_numeric($tsid)) continue;
      $traitPlotter = new TraitPlotManager("polar");
      if($tid) $traitPlotter->setTid($tid);
      $traitPlotter->setTraitStateId($tsid);
      echo '<div class="resource-title">'.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</div>';
      echo '<svg width="400" height="400" viewbox="0 0 ' . $traitPlotter->getViewboxWidth() . ' ' . $traitPlotter->getViewboxHeight() . ' role="img"><g>' . PHP_EOL;
      echo $traitPlotter->monthlyPolarPlot();
      echo '</g></svg>';
    }

	?>
</div>
