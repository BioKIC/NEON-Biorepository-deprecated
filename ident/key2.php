<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/KeyDataManager.php');
include_once($SERVER_ROOT.'/content/lang/ident/key.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

$isEditor = false;
if($IS_ADMIN || array_key_exists("KeyEditor",$USER_RIGHTS)){
	$isEditor = true;
}

$attrsValues = Array();

$clValue = array_key_exists("cl",$_REQUEST)?$_REQUEST["cl"]:"";
if(!$clValue && array_key_exists('clid',$_REQUEST)) $clValue = $_REQUEST['clid'];
$dynClid = array_key_exists("dynclid",$_REQUEST)?$_REQUEST["dynclid"]:0;
$taxonValue = array_key_exists("taxon",$_REQUEST)?$_REQUEST["taxon"]:"";
$action = array_key_exists("submitbutton",$_REQUEST)?$_REQUEST["submitbutton"]:"";
$rv = array_key_exists("rv",$_REQUEST)?$_REQUEST["rv"]:"";
$pid = array_key_exists('pid',$_REQUEST)?$_REQUEST['pid']:'';
$langValue = array_key_exists("lang",$_REQUEST)?$_REQUEST["lang"]:"";
$displayMode = array_key_exists("displaymode",$_REQUEST)?$_REQUEST["displaymode"]:"";
if(!$action && array_key_exists("attr",$_REQUEST) && is_array($_REQUEST["attr"])){
	$attrsValues = $_REQUEST["attr"];	//Array of: cid + "-" + cs (ie: 2-3)
}

//Sanitation
if(!is_numeric($dynClid)) $dynClid = 0;
if(!is_numeric($pid)) $pid = 0;
if(!is_numeric($rv)) $rv = '';
$langValue = 'English';

$dataManager = new KeyDataManager();

//if(!$langValue) $langValue = $defaultLang;
if($displayMode) $dataManager->setCommonDisplay(true);;
$dataManager->setLanguage($langValue);
if($pid) $dataManager->setProject($pid);
if($dynClid) $dataManager->setDynClid($dynClid);
$clid = $dataManager->setClValue($clValue);
if($taxonValue) $dataManager->setTaxonFilter($taxonValue);
if($attrsValues) $dataManager->setAttrs($attrsValues);
if($rv) $dataManager->setRelevanceValue($rv);

$data = $dataManager->getData();
$chars = $data["chars"];  				//$chars = Array(HTML Strings)
$taxa = $data["taxa"];					//$taxa  = Array(family => array(TID => DisplayName))

//Harevest and remove language list from $chars
$languages = Array();
if($chars){
	$languages = $chars["Languages"];
	unset($chars["Languages"]);
}
?>

<html>
<head>
	<title><?php echo $DEFAULT_TITLE.$LANG['WEBKEY'].preg_replace('/\<[^\>]+\>/','',$dataManager->getClName()); ?></title>
	<?php
	$activateJQuery = false;
	if(file_exists($SERVER_ROOT.'/includes/head.php')){
		include_once($SERVER_ROOT.'/includes/head.php');
	}
	else{
		echo '<link href="'.$CLIENT_ROOT.'/css/jquery-ui.css" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/base.css?ver=1" type="text/css" rel="stylesheet" />';
		echo '<link href="'.$CLIENT_ROOT.'/css/main.css?ver=1" type="text/css" rel="stylesheet" />';
	}
	include_once($SERVER_ROOT.'/includes/googleanalytics.php');
	?>
	<script type="text/javascript" src="../js/symb/ident.key.js"></script>
	<style type="text/css">
		fieldset { display: inline-block; float: right; padding: 5px 10px; min-width: 25% }
		legend { font-weight:bold }
		.editimg { width: 13px }
		#key-div {  }
		#key-chars { }
		#key-taxa { vertical-align: top; }
		.charHeading {}
		.characterStateName {}
		.dynam {}
		.dynamlang { margin-top: 0.5em; font-weight: bold; }
		.dynamopt {}
	</style>
</head>
<body>
<?php
$displayLeftMenu = (isset($ident_keyMenu)?$ident_keyMenu:true);
include($SERVER_ROOT.'/includes/header.php');
echo '<div class="navpath">';
echo '<a href="../index.php">'.$LANG['HOME'].'</a> &gt;&gt; ';
if($dynClid){
	if($dataManager->getClType() == 'Specimen Checklist'){
		echo '<a href="'.$CLIENT_ROOT.'/collections/list.php?tabindex=0">';
		echo 'Occurrence Checklist';
		echo '</a> &gt;&gt; ';
	}
}
elseif($clid){
	echo '<a href="'.$CLIENT_ROOT.'/checklists/checklist.php?clid='.$clid.'&pid='.$pid.'">';
	echo 'Checklist: '.$dataManager->getClName();
	echo '</a> &gt;&gt; ';
}
elseif($pid){
	echo '<a href="'.$CLIENT_ROOT.'/projects/index.php?pid='.$pid.'">';
	echo 'Project Checklists';
	echo '</a> &gt;&gt; ';
}
echo '<b>Key: '.$dataManager->getClName().'</b>';
echo '</div>';
?>
<div id="innertext">
	<?php
	if($isEditor){
		?>
		<div style="float:right;margin:15px;" title="Edit Character Matrix">
			<a href="tools/matrixeditor.php?clid=<?php echo $clid; ?>"><img class="editimg" src="../images/edit.png" /><span style="font-size:70%;">CM</span></a>
		</div>
		<?php
	}
	?>
	<h2>
		<?php
		if($FLORA_MOD_IS_ACTIVE) echo '<a href="../checklists/checklist.php?clid='.$clid.'&dynclid='.$dynClid.'&pid='.$pid.'">';
		echo $dataManager->getClName()." ";
		if($FLORA_MOD_IS_ACTIVE) echo '</a>';
		?>
	</h2>
	<fieldset>
		<legend>Filter/Display Options</legend>
		<div id="key-chars">
			<form name="keyform" id="keyform" action="key2.php" method="post">
				<div>
					<div><?php echo (isset($LANG['TAXON_SEARCH'])?$LANG['TAXON_SEARCH']:'Family/Genus Filter');?>:</div>
					<select name="taxon" onchange="this.form.submit();">
						<?php
						echo "<option value='All Species'>".$LANG['SELECTTAX']."</option>\n";
						$selectList = Array();
						$selectList = $dataManager->getTaxaFilterList();
						foreach($selectList as $value){
							$selectStr = ($value==$taxonValue?"SELECTED":"");
							echo "<option $selectStr>$value</option>\n";
						}
						?>
					</select>
				</div>
				<hr size="2" />
				<?php
				//echo "<div style=''>Relevance value: <input name='rv' type='text' size='3' title='Only characters with > ".($rv*100)."% relevance to the active spp. list will be displayed.' value='".$dataManager->getRelevanceValue()."'></div>";
				//List char Data with selected states checked
				if(count($languages) > 1){
					echo "<div id='langlist' style='margin:0.5em;'>Languages: <select name='lang' onchange='setLang(this);'>\n";
					foreach($languages as $l){
						echo "<option value='".$l."' ".($defaultLang == $l?"SELECTED":"").">$l</option>\n";
					}
					echo "</select></div>\n";
				}
				?>
				<div style="margin:5px">
					<?php echo $LANG['DISPLAY'].': '; ?>
					<select name="displaymode" onchange="this.form.submit();">
						<?php
						echo '<option value="0">'.$LANG['SCINAME'].'</option>';
						echo '<option value="1"'.($displayMode?' SELECTED':'').'>'.$LANG['COMMON'].'</option>';
						?>
					</select>
				</div>
				<?php
				if($chars){
					//echo "<div id='showall' class='dynamControl' style='display:none'><a href='#' onclick='javascript: toggleAll();'>Show All Characters</a></div>\n";
					//echo "<div class='dynamControl' style='display:block'><a href='#' onclick='javascript: toggleAll();'>Hide Advanced Characters</a></div>\n";
					foreach($chars as $key => $htmlStrings){
						echo $htmlStrings."\n";
					}
				}
				?>
				<div>
					<input type="hidden" id="cl" name="clid" value="<?php echo $clid; ?>" />
					<input type="hidden" id="dynclid" name="dynclid" value="<?php echo $dynClid; ?>" />
					<input type="hidden" id="pid" name="pid" value="<?php echo $pid; ?>" />
					<input type="hidden" id="rv" name="rv" value="<?php echo $dataManager->getRelevanceValue(); ?>" />
				</div>
			</form>
		</div>
	</fieldset>
	<div id="key-taxa">
		<?php
		//List taxa by family/sci name
		if(($clid && $taxonValue) || $dynClid){
			if(!$dynClid && $dataManager->getClAuthors()) echo '<div>'.$dataManager->getClAuthors().'</div>';
			$count = $dataManager->getTaxaCount();
			if($count > 0) echo '<div>'.$LANG['SPECCOUNT'].': '.$count.'</div>';
			else echo '<div>'.$LANG['NOMATCH'].'</div>';
			ksort($taxa);
			foreach($taxa as $family => $species){
				echo '<div><h3 style="margin-bottom:0px;margin-top:10px;">'.$family.'</h3></div>';
				natcasesort($species);
				foreach($species as $tid => $disName){
					$newSpLink = '../taxa/index.php?taxon='.$tid."&clid=".($dataManager->getClType()=="static"?$dataManager->getClid():"");
					echo '<div style="margin:0px 5px 0px 10px;">';
					echo '<a href="'.$newSpLink.'" target="_blank"><i>'.$disName.'</i></a> ';
					if($isEditor){
						echo '<a href="tools/editor.php?tid='.$tid.'&lang='.$DEFAULT_LANG.'" target="_blank">';
						echo '<img class="editimg" src="../images/edit.png" title="'.$LANG['EDITMORP'].'" />';
						echo '</a>';
					}
					echo '</div>';
				}
			}
		}
		else{
			echo $dataManager->getIntroHtml();
		}
		?>
	</div>
</div>
<?php
	include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>