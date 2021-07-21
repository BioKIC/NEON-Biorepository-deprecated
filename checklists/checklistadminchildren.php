<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ChecklistAdmin.php');
@include_once($SERVER_ROOT.'/content/lang/checklists/checklistadminchildren.'.$LANG_TAG.'.php');

$clid = array_key_exists("clid",$_REQUEST)?$_REQUEST["clid"]:0;
$pid = array_key_exists("pid",$_REQUEST)?$_REQUEST["pid"]:"";

$clManager = new ChecklistAdmin();
$clManager->setClid($clid);

?>
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
						$clArr = $clManager->getUserChecklistArr();
						foreach($clArr as $k => $name){
							echo '<option value="'.$k.'">'.$name.'</option>';
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
			if($childArr = $clManager->getChildrenChecklist()){
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
</div>