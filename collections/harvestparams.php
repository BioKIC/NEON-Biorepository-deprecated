<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/content/lang/collections/harvestparams.'.$LANG_TAG.'.php');
include_once($SERVER_ROOT.'/classes/OccurrenceManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collManager = new OccurrenceManager();
$searchVar = $collManager->getQueryTermStr();
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE.' '.$LANG['PAGE_TITLE']; ?></title>
	<?php
	$activateJQuery = true;
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
	<script src="../js/jquery-3.2.1.min.js?ver=3" type="text/javascript"></script>
	<script src="../js/jquery-ui-1.12.1/jquery-ui.min.js?ver=3" type="text/javascript"></script>
	<script src="../js/symb/collections.harvestparams.js?ver=180721" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
			if($searchVar){
				?>
				sessionStorage.querystr = "<?php echo $searchVar; ?>";
				<?php
			}
			?>
			setHarvestParamsForm();
		});
	</script>
	<script src="../js/symb/api.taxonomy.taxasuggest.js?ver=3" type="text/javascript"></script>
	<style type="text/css">
		hr{ clear:both; margin: 10px 0px }
		.categoryDiv { font-weight:bold; font-size: 18px }
		.coordBoxDiv { float:left; border:2px solid brown; padding:10px; margin:5px; white-space: nowrap; }
		.coordBoxDiv .labelDiv { font-weight:bold;float:left }
		.coordBoxDiv .iconDiv { float:right;margin-left:5px; }
		.coordBoxDiv .iconDiv img { width:18px; }
		.coordBoxDiv .elemDiv { clear:both; }
	</style>
</head>
<body>
<?php
	$displayLeftMenu = (isset($collections_harvestparamsMenu)?$collections_harvestparamsMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	if(isset($collections_harvestparamsCrumbs)){
		if($collections_harvestparamsCrumbs){
			echo '<div class="navpath">';
			echo $collections_harvestparamsCrumbs.' &gt;&gt; ';
			echo '<b>'.$LANG['NAV_SEARCH'].'</b>';
			echo '</div>';
		}
	}
	else{
		?>
		<div class='navpath'>
			<a href="../index.php"><?php echo $LANG['NAV_HOME']; ?></a> &gt;&gt;
			<a href="index.php"><?php echo $LANG['NAV_COLLECTIONS']; ?></a> &gt;&gt;
			<b><?php echo $LANG['NAV_SEARCH']; ?></b>
		</div>
		<?php
	}
	?>
	<div id="innertext">
		<form name="harvestparams" id="harvestparams" action="list.php" method="post" onsubmit="return checkHarvestParamsForm(this)">
			<hr/>
			<div>
				<div style="float:left">
					<div>
						<div class="categoryDiv"><?php echo $LANG['TAXON_HEADER']; ?></div>
						<div style="margin:10px 0px 0px 5px;"><input type='checkbox' name='usethes' value='1' CHECKED /><?php echo $LANG['INCLUDE_SYNONYMS']; ?></div>
					</div>
					<div>
						<select id="taxontype" name="taxontype">
							<?php
							$taxonType = 1;
							if(isset($DEFAULT_TAXON_SEARCH) && $DEFAULT_TAXON_SEARCH) $taxonType = $DEFAULT_TAXON_SEARCH;
							for($h=1;$h<6;$h++){
								echo '<option value="'.$h.'" '.($taxonType==$h?'SELECTED':'').'>'.$LANG['SELECT_1-'.$h].'</option>';
							}
							?>
						</select>
						<input id="taxa" type="text" size="60" name="taxa" value="" title="<?php echo $LANG['SEPARATE_MULTIPLE']; ?>" />
					</div>
				</div>
				<div style='float:right;margin:0px 10px;'>
					<div><button type="submit" style="width:100%"><?php echo isset($LANG['BUTTON_NEXT_LIST'])?$LANG['BUTTON_NEXT_LIST']:'List Display'; ?></button></div>
					<div><button type="button" style="width:100%" onclick="displayTableView(this.form)"><?php echo isset($LANG['BUTTON_NEXT_TABLE'])?$LANG['BUTTON_NEXT_TABLE']:'Table Display'; ?></button></div>
					<div><button type="reset" style="width:100%" onclick="resetHarvestParamsForm()"><?php echo isset($LANG['BUTTON_RESET'])?$LANG['BUTTON_RESET']:'Reset Form'; ?></button></div>
				</div>
			</div>
			<hr/>
			<div>
				<div class="categoryDiv"><?php echo $LANG['LOCALITY_CRITERIA']; ?></div>
			</div>
			<div>
				<?php echo $LANG['COUNTRY']; ?>: <input type="text" id="country" size="43" name="country" value="" title="<?php echo $LANG['SEPARATE_MULTIPLE']; ?>" />
			</div>
			<div>
				<?php echo $LANG['STATE']; ?>: <input type="text" id="state" size="37" name="state" value="" title="<?php echo $LANG['SEPARATE_MULTIPLE']; ?>" />
			</div>
			<div>
				<?php echo $LANG['COUNTY']; ?>: <input type="text" id="county" size="37"  name="county" value="" title="<?php echo $LANG['SEPARATE_MULTIPLE']; ?>" />
			</div>
			<div>
				<?php echo $LANG['LOCALITY']; ?>: <input type="text" id="locality" size="43" name="local" value="" />
			</div>
			<div>
				<?php echo $LANG['ELEV_INPUT_1']; ?>: <input type="text" id="elevlow" size="10" name="elevlow" value="" onchange="cleanNumericInput(this);" />
				<?php echo $LANG['ELEV_INPUT_2']; ?> <input type="text" id="elevhigh" size="10" name="elevhigh" value="" onchange="cleanNumericInput(this);" />
			</div>
			<hr>
			<div class="categoryDiv"><?php echo $LANG['LAT_LNG_HEADER']; ?></div>
			<div>
				<div class="coordBoxDiv">
					<div class="labelDiv">
						<?php echo $LANG['LL_BOUND_TEXT']; ?>
					</div>
					<div class="iconDiv">
						<a href="#" onclick="openCoordAid('rectangle');return false;"><img src="../images/map.png" title="<?php echo (isset($LANG['MAP_AID'])?$LANG['MAP_AID']:'Mapping Aid'); ?>" /></a>
					</div>
					<div class="elemDiv">
						<div>
							<?php echo $LANG['LL_BOUND_NLAT']; ?>: <input type="text" id="upperlat" name="upperlat" size="7" value="" onchange="cleanNumericInput(this);">
							<select id="upperlat_NS" name="upperlat_NS">
								<option id="ulN" value="N"><?php echo $LANG['LL_N_SYMB']; ?></option>
								<option id="ulS" value="S"><?php echo $LANG['LL_S_SYMB']; ?></option>
							</select>
						</div>
						<div>
							<?php echo $LANG['LL_BOUND_SLAT']; ?>: <input type="text" id="bottomlat" name="bottomlat" size="7" value="" onchange="cleanNumericInput(this);">
							<select id="bottomlat_NS" name="bottomlat_NS">
								<option id="blN" value="N"><?php echo $LANG['LL_N_SYMB']; ?></option>
								<option id="blS" value="S"><?php echo $LANG['LL_S_SYMB']; ?></option>
							</select>
						</div>
						<div>
							<?php echo $LANG['LL_BOUND_WLNG']; ?>: <input type="text" id="leftlong" name="leftlong" size="7" value="" onchange="cleanNumericInput(this);">
							<select id="leftlong_EW" name="leftlong_EW">
								<option id="llW" value="W"><?php echo $LANG['LL_W_SYMB']; ?></option>
								<option id="llE" value="E"><?php echo $LANG['LL_E_SYMB']; ?></option>
							</select>
						</div>
						<div>
							<?php echo $LANG['LL_BOUND_ELNG']; ?>: <input type="text" id="rightlong" name="rightlong" size="7" value="" onchange="cleanNumericInput(this);" style="margin-left:3px;">
							<select id="rightlong_EW" name="rightlong_EW">
								<option id="rlW" value="W"><?php echo $LANG['LL_W_SYMB']; ?></option>
								<option id="rlE" value="E"><?php echo $LANG['LL_E_SYMB']; ?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="coordBoxDiv">
					<div class="labelDiv">
						<?php echo isset($LANG['LL_POLYGON_TEXT'])?$LANG['LL_POLYGON_TEXT']:''; ?>
					</div>
					<div class="iconDiv">
						&nbsp;<a href="#" onclick="openCoordAid('polygon');return false;"><img src="../images/map.png" title="<?php echo (isset($LANG['MAP_AID'])?$LANG['MAP_AID']:'Mapping Aid'); ?>" /></a>
					</div>
					<div class="elemDiv">
						<textarea id="footprintwkt" name="footprintwkt" style="zIndex:999;width:100%;height:90px"></textarea>
					</div>
				</div>
				<div class="coordBoxDiv">
					<div class="labelDiv">
						<?php echo $LANG['LL_P-RADIUS_TEXT']; ?>
					</div>
					<div class="iconDiv">
						<a href="#" onclick="openCoordAid('circle');return false;"><img src="../images/map.png" title="<?php echo (isset($LANG['MAP_AID'])?$LANG['MAP_AID']:'Mapping Aid'); ?>" /></a>
					</div>
					<div class="elemDiv">
						<div>
							<?php echo $LANG['LL_P-RADIUS_LAT']; ?>: <input type="text" id="pointlat" name="pointlat" size="7" value="" onchange="cleanNumericInput(this);">
							<select id="pointlat_NS" name="pointlat_NS">
								<option id="N" value="N"><?php echo $LANG['LL_N_SYMB']; ?></option>
								<option id="S" value="S"><?php echo $LANG['LL_S_SYMB']; ?></option>
							</select>
						</div>
						<div>
							<?php echo $LANG['LL_P-RADIUS_LNG']; ?>: <input type="text" id="pointlong" name="pointlong" size="7" value="" onchange="cleanNumericInput(this);">
							<select id="pointlong_EW" name="pointlong_EW">
								<option id="W" value="W"><?php echo $LANG['LL_W_SYMB']; ?></option>
								<option id="E" value="E"><?php echo $LANG['LL_E_SYMB']; ?></option>
							</select>
						</div>
						<div>
							<?php echo $LANG['LL_P-RADIUS_RADIUS']; ?>: <input type="text" id="radius" name="radius" size="5" value="" onchange="cleanNumericInput(this);">
							<select id="radiusunits" name="radiusunits">
								<option value="km"><?php echo $LANG['LL_P-RADIUS_KM']; ?></option>
								<option value="mi"><?php echo $LANG['LL_P-RADIUS_MI']; ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<hr/>
			<div class="categoryDiv"><?php echo $LANG['COLLECTOR_HEADER']; ?></div>
			<div>
				<?php echo $LANG['COLLECTOR_LASTNAME']; ?>:
				<input type="text" id="collector" size="32" name="collector" value="" title="<?php echo $LANG['SEPARATE_MULTIPLE']; ?>" />
			</div>
			<div>
				<?php echo $LANG['COLLECTOR_NUMBER']; ?>:
				<input type="text" id="collnum" size="31" name="collnum" value="" title="<?php echo $LANG['TITLE_TEXT_2']; ?>" />
			</div>
			<div>
				<?php echo $LANG['COLLECTOR_DATE']; ?>:
				<input type="text" id="eventdate1" size="32" name="eventdate1" style="width:100px;" value="" title="<?php echo $LANG['TITLE_TEXT_3']; ?>" /> -
				<input type="text" id="eventdate2" size="32" name="eventdate2" style="width:100px;" value="" title="<?php echo $LANG['TITLE_TEXT_4']; ?>" />
			</div>
			<hr/>
			<div style="float:right;">
				<div><button type="submit" style="width:100%"><?php echo isset($LANG['BUTTON_NEXT_LIST'])?$LANG['BUTTON_NEXT_LIST']:'List Display'; ?></button></div>
				<div><button type="button" style="width:100%" onclick="displayTableView(this.form)"><?php echo isset($LANG['BUTTON_NEXT_TABLE'])?$LANG['BUTTON_NEXT_TABLE']:'Table Display'; ?></button></div>
			</div>
			<div>
				<div style="font-weight:bold; font-size: 18px"><?php echo $LANG['SPECIMEN_HEADER']; ?></div>
			</div>
			<div>
				<?php echo $LANG['CATALOG_NUMBER']; ?>:
				<input type="text" id="catnum" size="32" name="catnum" value="" title="<?php echo $LANG['SEPARATE_MULTIPLE']; ?>" />
				<input name="includeothercatnum" type="checkbox" value="1" checked /> <?php echo $LANG['INCLUDE_OTHER_CATNUM']?>
			</div>
			<div>
				<input type='checkbox' name='typestatus' value='1' /> <?php echo isset($LANG['TYPE'])?$LANG['TYPE']:'Limit to Type Specimens Only'; ?>
			</div>
			<div>
				<input type='checkbox' name='hasimages' value='1' /> <?php echo isset($LANG['HAS_IMAGE'])?$LANG['HAS_IMAGE']:'Limit to Specimens with Images Only'; ?>
			</div>
			<div>
				<input type='checkbox' name='hasgenetic' value='1' /> <?php echo isset($LANG['HAS_GENETIC'])?$LANG['HAS_GENETIC']:'Limit to Specimens with Genetic Data Only'; ?>
			</div>
			<div>
				<input type='checkbox' name='hascoords' value='1' /> <?php echo isset($LANG['HAS_COORDS'])?$LANG['HAS_COORDS']:'Limit to Specimens with Geocoordinates Only'; ?>
			</div>
			<div>
				<input type='checkbox' name='includecult' value='1' /> <?php echo isset($LANG['INCLUDE_CULTIVATED'])?$LANG['INCLUDE_CULTIVATED']:'Include cultivated/captive occurrences'; ?>
			</div>
			<div>
				<input type="hidden" name="reset" value="1" />
				<input type="hidden" name="db" value="<?php echo $collManager->getSearchTerm('db'); ?>" />
			</div>
		</form>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>
