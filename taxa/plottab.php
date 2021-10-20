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

  echo '<div id="tab-calendarplot" class="sptab">';
  foreach($traitstateids as $tsid) {
    if(!is_numeric($tsid)) continue;
    $traitPlotter = new TraitPlotManager("line");
    if($tid) $traitPlotter->setTid($tid);
    $traitPlotter->setTraitStateId($tsid);
    echo '<div class="resource-title">'.$traitPlotter->getTraitName(). ': '.$traitPlotter->getStateName().'</div>';
    echo '<svg height="300px" viewbox="0 0 ' . $traitPlotter->getViewboxWidth() . ' ' . $traitPlotter->getViewboxHeight() . '" ><g>' . PHP_EOL;
    echo $traitPlotter->monthlyPolarPlot();
    echo '</g></svg>';
    echo '<p class="PlotCaption">'.$traitPlotter->getPlotCaption().'</p>';
  }
  echo '</div>';

?>
