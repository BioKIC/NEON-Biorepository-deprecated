<?php
$gtsTermArr = $occManager->getPaleoGtsTerms();
?>
<script>
	var gtsArr = { <?php $d=''; foreach($gtsTermArr as $term => $rankid){ echo $d.'"'.$term.'":'.$rankid; $d=','; } ?> };
	function earlyIntervalChanged(elemObj){
		intervalChanged(elemObj);
		fieldChanged('earlyInterval');
	}

	function lateIntervalChanged(elemObj){
		intervalChanged(elemObj);
		fieldChanged('lateInterval');
	}

	function intervalChanged(elemObj){
		var term = elemObj.value;
		var rankid = gtsArr[term];
		if(rankid==10 || rankid==20){
			if($("select[name=eon]").val() == '') $("select[name=eon]").val(term);
		}
		else if(rankid==30){
			if($("select[name=era]").val() == '') $("select[name=era]").val(term);
		}
		else if(rankid==40){
			if($("select[name=period]").val() == '') $("select[name=period]").val(term);
		}
		else if(rankid==50){
			if($("select[name=epoch]").val() == '') $("select[name=epoch]").val(term);
		}
		else if(rankid==60){
			if($("select[name=stage]").val() == '') $("select[name=stage]").val(term);
		}
	}
</script>
<fieldset>
	<legend><b>Paleontology</b></legend>
	<div>
		<div id="earlyIntervalDiv">
			<?php echo (defined('EARLYINTERVALLABEL')?EARLYINTERVALLABEL:'Early Interval'); ?>
			<a href="#" onclick="return dwcDoc('earlyInterval')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="earlyInterval" onchange="earlyIntervalChanged(this)">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid){
					echo '<option value="'.$term.'" '.(isset($occArr['earlyInterval']) && $occArr['earlyInterval']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
			<!-- <input type="text" name="earlyInterval" value="<?php echo isset($occArr['earlyInterval'])?$occArr['earlyInterval']:''; ?>" onchange="fieldChanged('earlyInterval');" /> -->
		</div>
		<div id="lateIntervalDiv">
			<?php echo (defined('LATEINTERVALLABEL')?LATEINTERVALLABEL:'Late Interval'); ?>
			<a href="#" onclick="return dwcDoc('lateInterval')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="lateInterval" onchange="lateIntervalChanged(this)">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid){
					echo '<option value="'.$term.'" '.(isset($occArr['lateInterval']) && $occArr['lateInterval']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
			<!-- <input type="text" name="lateInterval" value="<?php echo isset($occArr['lateInterval'])?$occArr['lateInterval']:''; ?>" onchange="fieldChanged('lateInterval');" /> -->
		</div>
	</div>
	<div style="clear:both">
		<div id="eonDiv">
			<?php echo (defined('EONLABEL')?EONLABEL:'Eon'); ?>
			<a href="#" onclick="return dwcDoc('eon')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="eon" onchange="fieldChanged('eon');">
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
			<a href="#" onclick="return dwcDoc('era')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="era" onchange="fieldChanged('era');">
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
			<a href="#" onclick="return dwcDoc('period')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="period" onchange="fieldChanged('period');">
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
			<a href="#" onclick="return dwcDoc('epoch')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="epoch" onchange="fieldChanged('epoch');">
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
			<a href="#" onclick="return dwcDoc('stage')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="stage" onchange="fieldChanged('stage');">
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
	<div style="clear:both">
		<div id="absoluteAgeDiv">
			<?php echo (defined('ABSOLUTEAGELABEL')?ABSOLUTEAGELABEL:'Absolute Age'); ?>
			<a href="#" onclick="return dwcDoc('absoluteAge')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="absoluteAge" onchange="fieldChanged('absoluteAge');">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid){
					if($rankid == 60) echo '<option value="'.$term.'" '.(isset($occArr['absoluteAge']) && $occArr['absoluteAge']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
			<!-- <input type="text" name="absoluteAge" value="<?php echo isset($occArr['absoluteAge'])?$occArr['absoluteAge']:''; ?>" onchange="fieldChanged('absoluteAge');" /> -->
		</div>
		<div id="storageAgeDiv">
			<?php echo (defined('STORAGEAGELABEL')?STORAGEAGELABEL:'Storage Age'); ?>
			<a href="#" onclick="return dwcDoc('storageAge')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<select name="storageAge" onchange="fieldChanged('storageAge');">
				<option value=""></option>
				<?php
				foreach($gtsTermArr as $term => $rankid){
					if($rankid == 60) echo '<option value="'.$term.'" '.(isset($occArr['storageAge']) && $occArr['storageAge']==$term?'SELECTED':'').'>'.$term.'</option>';
				}
				?>
			</select>
			<!-- <input type="text" name="storageAge" value="<?php echo isset($occArr['storageAge'])?$occArr['storageAge']:''; ?>" onchange="fieldChanged('storageAge');" />  -->
		</div>
		<div id="localStageDiv">
			<?php echo (defined('LOCALSTAGELABEL')?LOCALSTAGELABEL:'Local Stage'); ?>
			<a href="#" onclick="return dwcDoc('localStage')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="localStage" value="<?php echo isset($occArr['localStage'])?$occArr['localStage']:''; ?>" onchange="fieldChanged('localStage');" />
		</div>
	</div>
	<div style="clear:both">
		<div id="biozoneDiv">
			<?php echo (defined('BIOZONELABEL')?BIOZONELABEL:'Biozone'); ?>
			<a href="#" onclick="return dwcDoc('biozone')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="biozone" value="<?php echo isset($occArr['biozone'])?$occArr['biozone']:''; ?>" onchange="fieldChanged('biozone');" />
		</div>
		<div id="biostratigraphyDiv">
			<?php echo (defined('BIOSTRATIGRAPHYLABEL')?BIOSTRATIGRAPHYLABEL:'Biostratigraphy (Flora/Fauna)'); ?>
			<a href="#" onclick="return dwcDoc('biostratigraphy')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="biostratigraphy" value="<?php echo isset($occArr['biostratigraphy'])?$occArr['biostratigraphy']:''; ?>" onchange="fieldChanged('biostratigraphy');" />
		</div>
		<div id="lithoGroupDiv">
			<?php echo (defined('LITHOGROUPLABEL')?LITHOGROUPLABEL:'Group'); ?>
			<a href="#" onclick="return dwcDoc('group')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="lithogroup" value="<?php echo isset($occArr['lithogroup'])?$occArr['lithogroup']:''; ?>" onchange="fieldChanged('lithogroup');" />
		</div>
		<div id="formationDiv">
			<?php echo (defined('FORMATIONLABEL')?FORMATIONLABEL:'Formation'); ?>
			<a href="#" onclick="return dwcDoc('formation')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="formation" value="<?php echo isset($occArr['formation'])?$occArr['formation']:''; ?>" onchange="fieldChanged('formation');" />
		</div>
		<div id="taxonEnvironmentDiv">
			<?php echo (defined('TAXONENVIRONMENTLABEL')?TAXONENVIRONMENTLABEL:'Taxon Environment (Formation Marine)'); ?>
			<a href="#" onclick="return dwcDoc('taxonEnvironment')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="taxonEnvironment" value="<?php echo isset($occArr['taxonEnvironment'])?$occArr['taxonEnvironment']:''; ?>" onchange="fieldChanged('taxonEnvironment');" />
		</div>
		<div id="memberDiv">
			<?php echo (defined('MEMBERLABEL')?MEMBERLABEL:'Member'); ?>
			<a href="#" onclick="return dwcDoc('member')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="member" value="<?php echo isset($occArr['member'])?$occArr['member']:''; ?>" onchange="fieldChanged('member');" />
		</div>
		<div id="lithologyDiv">
			<?php echo (defined('LITHOLOGYLABEL')?LITHOLOGYLABEL:'Lithology'); ?>
			<a href="#" onclick="return dwcDoc('lithology')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="lithology" value="<?php echo isset($occArr['lithology'])?$occArr['lithology']:''; ?>" onchange="fieldChanged('lithology');" />
		</div>
	</div>
	<div style="clear:both">
		<div id="stratRemarksDiv">
			<?php echo (defined('TAXONENVIRONMENTLABEL')?TAXONENVIRONMENTLABEL:'Stratigraphy Remarks'); ?>
			<a href="#" onclick="return dwcDoc('stratRemarks')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="stratRemarks" value="<?php echo isset($occArr['stratRemarks'])?$occArr['stratRemarks']:''; ?>" onchange="fieldChanged('stratRemarks');" />
		</div>
	</div>
	<div style="clear:both">
		<div id="lithDescriptionDiv">
			<?php echo (defined('LITHDESCRIPTIONLABEL')?TAXONENVIRONMENTLABEL:'Lithology Description'); ?>
			<a href="#" onclick="return dwcDoc('lithDescription')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="lithDescription" value="<?php echo isset($occArr['lithDescription'])?$occArr['lithDescription']:''; ?>" onchange="fieldChanged('lithDescription');" />
		</div>
	</div>
	<div style="clear:both">
		<div id="elementDiv">
			<?php echo (defined('ELEMENTLABEL')?ELEMENTLABEL:'Element'); ?>
			<a href="#" onclick="return dwcDoc('element')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="element" value="<?php echo isset($occArr['element'])?$occArr['element']:''; ?>" onchange="fieldChanged('element');" />
		</div>
		<div id="slidePropertiesDiv">
			<?php echo (defined('SLIDEPROPERTIESLABEL')?SLIDEPROPERTIESLABEL:'Slide Properties'); ?>
			<a href="#" onclick="return dwcDoc('slideProperties')"><img class="docimg" src="../../images/qmark.png" /></a><br/>
			<input type="text" name="slideProperties" value="<?php echo isset($occArr['slideProperties'])?$occArr['slideProperties']:''; ?>" onchange="fieldChanged('slideProperties');" />
		</div>
	</div>
</fieldset>