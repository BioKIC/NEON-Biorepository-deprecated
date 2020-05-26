<?php
$gtsTermArr = $occManager->getPaleoGtsTerms();
?>
<script>
	var gtsArr = { <?php $d=''; foreach($gtsTermArr as $term => $rankid){ echo $d.'"'.$term.'":'.$rankid; $d=','; } ?> };
	function earlyIntervalChanged(elemObj){
		paloIntervalChanged(elemObj);
		fieldChanged('earlyInterval');
	}

	function lateIntervalChanged(elemObj){
		paloIntervalChanged(elemObj);
		fieldChanged('lateInterval');
	}

	function paloIntervalChanged(elemObj){
		var term = elemObj.value;
		var rankid = gtsArr[term];
		if(rankid==10 || rankid==20){
			if($("select[name=eon]").val() == '') $("select[name=eon]").val(term);
		}
		else if(rankid==30){
			if($("select[name=era]").val() == '') $("select[name=era]").val(term);
			setPaloParents("era");
		}
		else if(rankid==40){
			if($("select[name=period]").val() == '') $("select[name=period]").val(term);
			setPaloParents("period");
		}
		else if(rankid==50){
			if($("select[name=epoch]").val() == '') $("select[name=epoch]").val(term);
			setPaloParents("epoch");
		}
		else if(rankid==60){
			if($("select[name=stage]").val() == '') $("select[name=stage]").val(term);
			setPaloParents("stage");
		}
	}

	function setPaloParents(timePeriod){
		if(timePeriod){
			fieldChanged(timePeriod);
			switch(timePeriod) {
				case "eon":
					$("select[name=era]").val("");
				case "era":
					$("select[name=period]").val("");
				case "period":
					$("select[name=epoch]").val("");
				case "epoch":
					$("select[name=stage]").val("");
			}
			var childValue = $("select[name="+timePeriod+"]").val();
			if(childValue){
				if($("select[name=earlyInterval]").val() == "") $("select[name=earlyInterval]").val(childValue);
				if($("select[name=lateInterval]").val() == "") $("select[name=lateInterval]").val(childValue);
				if(timePeriod != "eon"){
					$.ajax({
						type: "POST",
						url: "rpc/getPaleoGtsParents.php",
						dataType: "json",
						data: { term: childValue }
					}).done(function( gtsObj ) {
					  	for (i = 0; i < gtsObj.length; i++) {
					  		var rankid = gtsObj[i].rankid;
							if(rankid == 10 || rankid == 20){
								if($("select[name=eon]").val() == "") $("select[name=eon]").val(gtsObj[i].value);
							}
							else if(rankid == 30){
								if($("select[name=era]").val() == "") $("select[name=era]").val(gtsObj[i].value);
							}
							else if(rankid == 40){
								if($("select[name=period]").val() == "") $("select[name=period]").val(gtsObj[i].value);
							}
							else if(rankid == 50){
								if($("select[name=epoch]").val() == "") $("select[name=epoch]").val(gtsObj[i].value);
							}
					  	}
					});
				}
			}
		}
	}
</script>
<fieldset>
	<legend>Paleontology</legend>
	<div style="clear:both">
		<div id="eonDiv">
			<?php echo (defined('EONLABEL')?EONLABEL:'Eon'); ?>
			<a href="#" onclick="return dwcDoc('eon')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="eon" onchange="setPaloParents('eon');">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid ){
					if($rankid < 30) echo '<option value="'.$term.'" '.(isset($occArr['eon']) && $occArr['eon']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
			<!-- <input type="text" name="eon" value="<?php echo isset($occArr['eon'])?$occArr['eon']:''; ?>" onchange="fieldChanged('eon');" /> -->
		</div>
		<div id="eraDiv">
			<?php echo (defined('ERALABEL')?ERALABEL:'Era'); ?>
			<a href="#" onclick="return dwcDoc('era')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="era" onchange="setPaloParents('era');">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid ){
					if($rankid == 30) echo '<option value="'.$term.'" '.(isset($occArr['era']) && $occArr['era']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
			<!-- <input type="text" name="era" value="<?php echo isset($occArr['era'])?$occArr['era']:''; ?>" onchange="fieldChanged('era');" />  -->
		</div>
		<div id="periodDiv">
			<?php echo (defined('PERIODLABEL')?PERIODLABEL:'Period'); ?>
			<a href="#" onclick="return dwcDoc('period')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="period" onchange="setPaloParents('period');">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid){
					if($rankid == 40) echo '<option value="'.$term.'" '.(isset($occArr['period']) && $occArr['period']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
			<!-- <input type="text" name="period" value="<?php echo isset($occArr['period'])?$occArr['period']:''; ?>" onchange="fieldChanged('period');" /> -->
		</div>
		<div id="epochDiv">
			<?php echo (defined('EPOCHLABEL')?EPOCHLABEL:'Epoch'); ?>
			<a href="#" onclick="return dwcDoc('epoch')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="epoch" onchange="setPaloParents('epoch');">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid){
					if($rankid == 50) echo '<option value="'.$term.'" '.(isset($occArr['epoch']) && $occArr['epoch']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
			<!-- <input type="text" name="epoch" value="<?php echo isset($occArr['epoch'])?$occArr['epoch']:''; ?>" onchange="fieldChanged('epoch');" />  -->
		</div>
		<div id="stageDiv">
			<?php echo (defined('STAGELABEL')?STAGELABEL:'Stage'); ?>
			<a href="#" onclick="return dwcDoc('stage')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="stage" onchange="setPaloParents('stage');">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid){
					if($rankid == 60) echo '<option value="'.$term.'" '.(isset($occArr['stage']) && $occArr['stage']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
			<!-- <input type="text" name="stage" value="<?php echo isset($occArr['stage'])?$occArr['stage']:''; ?>" onchange="fieldChanged('stage');" />  -->
		</div>
	</div>
	<div>
		<div id="earlyIntervalDiv">
			<?php echo (defined('EARLYINTERVALLABEL')?EARLYINTERVALLABEL:'Early Interval'); ?>
			<a href="#" onclick="return dwcDoc('earlyInterval')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="earlyinterval" onchange="earlyIntervalChanged(this)">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid){
					echo '<option value="'.$term.'" '.(isset($occArr['earlyinterval']) && $occArr['earlyinterval']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
		</div>
		<div id="lateIntervalDiv">
			<?php echo (defined('LATEINTERVALLABEL')?LATEINTERVALLABEL:'Late Interval'); ?>
			<a href="#" onclick="return dwcDoc('lateInterval')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="lateinterval" onchange="lateIntervalChanged(this)">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid){
					echo '<option value="'.$term.'" '.(isset($occArr['lateinterval']) && $occArr['lateinterval']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
		</div>
	</div>
	<div style="clear:both">
		<div id="absoluteAgeDiv">
			<?php echo (defined('ABSOLUTEAGELABEL')?ABSOLUTEAGELABEL:'Absolute Age'); ?>
			<a href="#" onclick="return dwcDoc('absoluteAge')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="absoluteage" value="<?php echo isset($occArr['absoluteage'])?$occArr['absoluteage']:''; ?>" onchange="fieldChanged('absoluteage');" />
		</div>
		<div id="storageAgeDiv">
			<?php echo (defined('STORAGEAGELABEL')?STORAGEAGELABEL:'Storage Age'); ?>
			<a href="#" onclick="return dwcDoc('storageAge')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="storageage" value="<?php echo isset($occArr['storageage'])?$occArr['storageage']:''; ?>" onchange="fieldChanged('storageage');" />
		</div>
		<div id="localStageDiv">
			<?php echo (defined('LOCALSTAGELABEL')?LOCALSTAGELABEL:'Local Stage'); ?>
			<a href="#" onclick="return dwcDoc('localStage')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="localstage" value="<?php echo isset($occArr['localstage'])?$occArr['localstage']:''; ?>" onchange="fieldChanged('localstage');" />
		</div>
	</div>
	<div style="clear:both">
		<div id="biotaDiv">
			<?php echo (defined('BIOTALABEL')?BIOTALABEL:'Biota (Flora/Fauna)'); ?>
			<a href="#" onclick="return dwcDoc('biota')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="biota" value="<?php echo isset($occArr['biota'])?$occArr['biota']:''; ?>" onchange="fieldChanged('biota');" />
		</div>
		<div id="biostratigraphyDiv">
			<?php echo (defined('BIOSTRATIGRAPHYLABEL')?BIOSTRATIGRAPHYLABEL:'Biostratigraphy (Biozone)'); ?>
			<a href="#" onclick="return dwcDoc('biostratigraphy')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="biostratigraphy" value="<?php echo isset($occArr['biostratigraphy'])?$occArr['biostratigraphy']:''; ?>" onchange="fieldChanged('biostratigraphy');" />
		</div>
		<div id="taxonEnvironmentDiv">
			<?php echo (defined('TAXONENVIRONMENTLABEL')?TAXONENVIRONMENTLABEL:'Taxon Environment (Formation Marine)'); ?>
			<a href="#" onclick="return dwcDoc('taxonEnvironment')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<?php
			$taxonEnvir = '';
			if(isset($occArr['taxonenvironment'])) $taxonEnvir = $occArr['taxonenvironment'];
			?>
			<select name="taxonenvironment" onchange="fieldChanged('taxonenvironment');">
				<option value=""></option>
				<option <?php if($taxonEnvir=='marine') echo 'SELECTED'; ?>>marine</option>
				<option<?php if($taxonEnvir=='non-marine') echo 'SELECTED'; ?>>non-marine</option>
				<option<?php if($taxonEnvir=='marine and non-marine') echo 'SELECTED'; ?>>marine and non-marine</option>
			</select>
		</div>
	</div>
	<div style="clear:both">
		<div id="lithoGroupDiv">
			<?php echo (defined('LITHOGROUPLABEL')?LITHOGROUPLABEL:'Group'); ?>
			<a href="#" onclick="return dwcDoc('group')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="lithogroup" value="<?php echo isset($occArr['lithogroup'])?$occArr['lithogroup']:''; ?>" onchange="fieldChanged('lithogroup');" />
		</div>
		<div id="formationDiv">
			<?php echo (defined('FORMATIONLABEL')?FORMATIONLABEL:'Formation'); ?>
			<a href="#" onclick="return dwcDoc('formation')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="formation" value="<?php echo isset($occArr['formation'])?$occArr['formation']:''; ?>" onchange="fieldChanged('formation');" />
		</div>
		<div id="memberDiv">
			<?php echo (defined('MEMBERLABEL')?MEMBERLABEL:'Member'); ?>
			<a href="#" onclick="return dwcDoc('member')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="member" value="<?php echo isset($occArr['member'])?$occArr['member']:''; ?>" onchange="fieldChanged('member');" />
		</div>
		<div id="bedDiv">
			<?php echo (defined('BEDLABEL')?BEDLABEL:'Bed'); ?>
			<a href="#" onclick="return dwcDoc('bed')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="bed" value="<?php echo isset($occArr['bed'])?$occArr['bed']:''; ?>" onchange="fieldChanged('bed');" />
		</div>
	</div>
	<div style="clear:both">
		<div id="lithologyDiv">
			<?php echo (defined('LITHOLOGYLABEL')?LITHOLOGYLABEL:'Lithology'); ?>
			<a href="#" onclick="return dwcDoc('lithology')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="lithology" value="<?php echo isset($occArr['lithology'])?$occArr['lithology']:''; ?>" onchange="fieldChanged('lithology');" />
		</div>
	</div>
	<div style="clear:both">
		<div id="stratRemarksDiv">
			<?php echo (defined('TAXONENVIRONMENTLABEL')?TAXONENVIRONMENTLABEL:'Remarks'); ?>
			<a href="#" onclick="return dwcDoc('stratRemarks')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="stratremarks" value="<?php echo isset($occArr['stratremarks'])?$occArr['stratremarks']:''; ?>" onchange="fieldChanged('stratremarks');" />
		</div>
	</div>
	<div style="clear:both">
		<div id="elementDiv">
			<?php echo (defined('ELEMENTLABEL')?ELEMENTLABEL:'Element'); ?>
			<a href="#" onclick="return dwcDoc('element')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="element" value="<?php echo isset($occArr['element'])?$occArr['element']:''; ?>" onchange="fieldChanged('element');" />
		</div>
		<div id="slidePropertiesDiv">
			<?php echo (defined('SLIDEPROPERTIESLABEL')?SLIDEPROPERTIESLABEL:'Slide Properties'); ?>
			<a href="#" onclick="return dwcDoc('slideProperties')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="slideproperties" value="<?php echo isset($occArr['slideproperties'])?$occArr['slideproperties']:''; ?>" onchange="fieldChanged('slideproperties');" />
		</div>
		<div id="geologicalContextIdDiv">
			<?php echo (defined('GEOLOGICALCONTEXTIDLABEL')?GEOLOGICALCONTEXTIDLABEL:'Context ID'); ?>
			<a href="#" onclick="return dwcDoc('geologicalContextID')" tabindex="-1"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="geologicalcontextid" value="<?php echo isset($occArr['geologicalcontextid'])?$occArr['geologicalcontextid']:''; ?>" onchange="fieldChanged('geologicalcontextid');" />
		</div>
	</div>
</fieldset>