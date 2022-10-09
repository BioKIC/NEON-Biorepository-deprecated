<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistAdmin.php');
@include_once($SERVER_ROOT.'/content/lang/checklists/checklistadminchildren.'.$LANG_TAG.'.php');

$clid = array_key_exists('clid',$_REQUEST)?$_REQUEST['clid']:0;
$pid = array_key_exists('pid',$_REQUEST)?$_REQUEST['pid']:'';
$targetClid = array_key_exists('targetclid',$_POST)?$_POST['targetclid']:'';
$parentClid = array_key_exists('parentclid',$_POST)?$_POST['parentclid']:'';
$targetPid = array_key_exists('targetpid',$_POST)?$_POST['targetpid']:'';
$copyAttributes = array_key_exists('copyattributes',$_POST)?$_POST['copyattributes']:'';

//Sanitation
if(!is_numeric($clid)) $clid = 0;
if(!is_numeric($pid)) $pid = 0;
if(!is_numeric($targetClid)) $targetClid = 0;
if(!is_numeric($parentClid)) $parentClid = '';
if(!is_numeric($targetPid)) $targetPid = 0;
if(!is_numeric($copyAttributes)) $copyAttributes = 0;

$clManager = new ChecklistAdmin();
$clManager->setClid($clid);

$clArr = $clManager->getUserChecklistArr();
$childArr = $clManager->getChildrenChecklist()
?>
<script src="../js/jquery-3.2.1.min.js?ver=3" type="text/javascript"></script>
<script src="../js/jquery-ui/jquery-ui.min.js?ver=3" type="text/javascript"></script>
<link href="../js/jquery-ui/jquery-ui.min.css" type="text/css" rel="Stylesheet" />
<script>
	$("#taxon").autocomplete({
		source: function( request, response ) {
			$.getJSON( "<?php echo $CLIENT_ROOT; ?>/rpc/taxasuggest.php", { term: request.term }, response );
		},
		minLength: 3,
		autoFocus: true,
		select: function( event, ui ) {
			if(ui.item){
				$("#parsetid").val(ui.item.id);
			}
		}
	});

	function validateParseChecklistForm(){

	}

	function validateAddChildForm(f){

	}
</script>
<style>
	.section-div{ margin-bottom: 3px; }
	#taxa{ width:400px }
	#parsetid{ width:100px }
</style>
<!-- inner text -->
<div id="innertext" style="background-color:white;">
	<div style="float:right;">
		<a href="#" onclick="toggle('addchilddiv')"><img src="../images/add.png" /></a>
	</div>
	<div style="margin:15px;font-weight:bold;font-size:120%;">
		<u><?php echo (isset($LANG['CHILD_CHECKLIST'])?$LANG['CHILD_CHECKLIST']:'Children Checklists'); ?></u>
	</div>
	<div style="margin:25px;clear:both;">
		<?php echo (isset($LANG['CHILD_DESCRIBE'])?$LANG['CHILD_DESCRIBE']:'Checklists will inherit scientific names, vouchers, notes, etc from all children checklists.
		Adding a new taxon or voucher to a child checklist will automatically add it to all parent checklists.
		The parent child relationship can transcend multiple levels (e.g. country &lt;- state &lt;- county).
		Note that only direct child can be removed.'); ?>
	</div>
	<div id="addchilddiv" style="margin:15px;display:none;">
		<fieldset style="padding:15px;">
			<legend><b><?php echo (isset($LANG['LINK_NEW'])?$LANG['LINK_NEW']:'Link New Checklist'); ?></b></legend>
			<form name="addchildform" target="checklistadmin.php" method="post" onsubmit="validateAddChildForm(this)">
				<div style="margin:10px;">
					<select name="clidadd">
						<option value=""><?php echo (isset($LANG['SELECT_CHILD'])?$LANG['SELECT_CHILD']:'Select Child Checklist'); ?></option>
						<option value="">-------------------------------</option>
						<?php
						foreach($clArr as $k => $name){
							if(!isset($childArr[$k])) echo '<option value="'.$k.'">'.$name.'</option>';
						}
						?>
					</select>
				</div>
				<div style="margin:10px;">
					<button name="submitaction" type="submit" value="Add Child Checklist"><?php echo (isset($LANG['ADD_CHILD'])?$LANG['ADD_CHILD']:'Add Child Checklist'); ?></button>
					<input name="clid" type="hidden" value="<?php echo $clid; ?>" />
					<input name="pid" type="hidden" value="<?php echo $pid; ?>" />
					<input name="tabindex" type="hidden" value="2" />
				</div>
			</form>
		</fieldset>
	</div>
	<div style="margin:15px;">
		<ul>
			<?php
			if($childArr){
				foreach($childArr as $k => $cArr){
					?>
					<li>
						<a href="checklist.php?clid=<?php echo $k; ?>"><?php echo $cArr['name']; ?></a>
						<?php
						if($cArr['pclid'] == $clid){
							$confirmStr = (isset($LANG['SURE'])?$LANG['SURE']:'Are you sure you want to remove').$cArr['name'].(isset($LANG['AS_CHILD'])?$LANG['AS_CHILD']:'as a child checklist');
							echo '<a href="checklistadmin.php?submitaction=delchild&tabindex=2&cliddel='.$k.'&clid='.$clid.'&pid='.$pid.'" onclick="return confirm(\''.$confirmStr.'\')">';
							echo '<img src="../images/del.png" style="width:14px;" /></a>';
							echo '</a>';
						}
						?>
					</li>
					<?php
				}
			}
			else{
				echo '<div style="font-size:110%;">'.(isset($LANG['NO_CHILDREN'])?$LANG['NO_CHILDREN']:'There are no Children Checklists').'</div>';
			}
			?>
		</ul>
	</div>
	<div style="margin:30px 15px;font-weight:bold;font-size:120%;">
		<u><?php echo (isset($LANG['PARENTS'])?$LANG['PARENTS']:'Parent Checklists'); ?></u>
	</div>
	<div style="margin:15px;">
		<ul>
			<?php
			if($parentArr = $clManager->getParentChecklists()){
				foreach($parentArr as $k => $name){
					?>
					<li>
						<a href="checklist.php?clid=<?php echo $k; ?>"><?php echo $name; ?></a>
					</li>
					<?php
				}
			}
			else{
				echo '<div style="font-size:110%;">'.(isset($LANG['NO_PARENTS'])?$LANG['NO_PARENTS']:'There are no Parent Checklists').'</div>';
			}
			?>
		</ul>
	</div>
	<div style="margin:15px;">
		<fieldset>
			<legend>Batch Parse Species List</legend>
			<form name="parsechecklistform" target="checklistadmin.php" method="post" onsubmit="validateParseChecklistForm(this)">
				<div class="section-div">
					<label>Taxonomic Node:</label>
					<input id="taxon" type="text" name="taxon" required />
					<input id="parsetid" name="tid" type="text" required >
				</div>
				<div class="section-div">
					<label>Target checklist:</label>
					<select name="targetclid">
						<option value="0">Create New Checklist</option>
						<?php
						foreach($clArr as $k => $name){
							if(!isset($childArr[$k])) echo '<option value="'.$k.'"'.($targetClid == $k?'SELECTED':'').'>'.$name.'</option>';
						}
						?>
					</select>
				</div>
				<div class="section-div">
					<label>Link to Parent Checklist:</label>
					<select name="parentclid">
						<option value="">No Parent Checklist</option>
						<option value="0" <?php if($parentClid === 0) echo 'SELECTED'; ?>>Create New Checklist</option>
						<?php
						foreach($clArr as $k => $name){
							if(!isset($childArr[$k])) echo '<option value="'.$k.'"'.($parentClid == $k?'SELECTED':'').'>'.$name.'</option>';
						}
						?>
					</select>
				</div>
				<div class="section-div">
					<label>Add to Project:</label>
					<select name="targetpid">
						<option value="">--no action--</option>
						<option value="0">New Project</option>
						<?php
						$projArr = $clManager->getUserProjectArr();
						foreach($projArr as $k => $name){
							echo '<option value="'.$k.'" '.($targetPid == $k?'SELECTED':'').'>'.$name.'</option>';
						}
						?>
					</select>
				</div>
				<div class="section-div">
					<input name="copyattributes" type="checkbox" <?php if($copyAttributes) echo 'checked'; ?>>
					<label>copy over permission and general attributes</label>
				</div>
				<div class="section-div">
					<button name="submitaction" type="submit" value="parseChecklist">Parse Checklist</button>
				</div>
			</form>
			<div><a href="<?php echo $CLIENT_ROOT; ?>/taxa/taxonomy/taxonomydisplay.php" target="_blank">Open Taxonomic Thesaurus Explorer</a></div>
		</fieldset>
	</div>
</div>