<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/taxa/index.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/TaxonProfile.php');
Header('Content-Type: text/html; charset='.$CHARSET);

$tid = $_REQUEST['tid'];
$taxAuthId = array_key_exists('taxauthid',$_REQUEST)?$_REQUEST['taxauthid']:1;

//Sanitation
if(!is_numeric($tid)) $tid = 0;

$taxonManager = new TaxonProfile();
$taxonManager->setTid($tid);

/*
$isEditor = false;
if($SYMB_UID){
	if($IS_ADMIN || array_key_exists('TaxonProfile',$USER_RIGHTS)){
		$isEditor = true;
	}
}
*/
?>
<div id="tab-resource" class="sptab">
	<?php
	echo '<div class="resource-title">'.(isset($LANG['INTERNAL_RESOURCES'])?$LANG['INTERNAL_RESOURCES']:'Internal Resources').'</div>';
	echo '<ul>';
	$occNum = $taxonManager->getOccTaxonInDbCnt();
	if($occNum > -1){
		$occMsg = number_format($occNum).' '.(isset($LANG['OCCURRENCES'])?'occurrences':'');
		if($occNum){
			$occHref = '../collections/list.php?db=all&includeothercatnum=1&taxa='.$taxonManager->getTaxonName().'&usethes=1';
			$occMsg = '<a class="btn" href="'.$occHref.'" target="_blank">'.$occMsg.'</a>';
		}
		echo '<li>'.$occMsg.'</li>';
	}
	echo '<li><a href="taxonomy/taxonomydynamicdisplay.php?target='.$tid.'" target="_blank">Taxonomic Tree</a></li>';
	echo '</ul>';
	//TODO: list other internal resources such as Taxon Traits, etc

	if($linkArr = $taxonManager->getLinkArr()){
		echo '<div class="resource-title">'.(isset($LANG['EXTERNAL_RESOURCES'])?$LANG['EXTERNAL_RESOURCES']:'External Resources').'</div>';
		echo '<ul>';
		foreach($linkArr as $linkObj){
			echo '<li><a href="'.$linkObj['url'].'" target="_blank">'.$linkObj['title'].'</a></li>';
			if($linkObj['notes']) echo '<li style="margin-left:10px">'.$linkObj['notes'].'</li>';
		}
		echo '</ul>';
	}
	?>
</div>