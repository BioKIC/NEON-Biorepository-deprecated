<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProfile.php');
header("Content-Type: text/html; charset=".$CHARSET);

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
if(!is_numeric($collid)) $collid = 0;

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
elseif($collid){
	if(array_key_exists('CollAdmin',$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])){
		$isEditor = true;
	}
}

if($collid && $isEditor){
	$collManager = new OccurrenceCollectionProfile('write');
	$collManager->setCollid($collid);
	$collMetaArr = current($collManager->getCollectionMetadata());

	$defaultLang = 'en';
	if(isset($EXTENDED_LANG) && $EXTENDED_LANG) $defaultLang = $EXTENDED_LANG;
	elseif(isset($DEFAULT_LANG)) $defaultLang = strtolower($DEFAULT_LANG);
	$langArr = explode(',',$defaultLang);
	?>
	<style>
		.link-div{ margin:10px 0px; }
		.link-div a{ margin-right:10px; }
		.link-div img{ width:13px; }
		.title-div{ margin-left: 10px; }
		.form-button{ margin:10px }
		#contact-listing{ padding: 10px 0px }
		.contact-div{ margin:10px 0px; }
		.contact-div a{ margin: 0px 10px; }
		.contact-div img{ width:13px; }
		#editContact-span{ display:none; }
		hr{ margin:10px 0px; }
	</style>
	<div id="contacts_resources">
		<fieldset>
			<legend>Link Resource Listing</legend>
			<div id="link-listing">
				<?php
				if($resourceArr = json_decode($collMetaArr['resourcejson'],true)){
					foreach($resourceArr as $key => $valueArr){
						echo '<div class="link-div"><span class="label">Link:</span> ';
						echo '<a href="'.$valueArr['url'].'" target="_blank">'.$valueArr['url'].'</a>';
						echo '<a href="#" onclick="editLink('.$key.');return false"><img src="../../images/edit.png" /></a>';
						echo '<a href="#" onclick="deleteLink('.$key.');return false"><img src="../../images/del.png" /></a>';
						foreach($valueArr['title'] as $langCode => $titleValue){
							$langStr = $langCode;
							if($langCode == 'en') $langStr = 'English';
							else if($langCode == 'es') $langStr = 'Spanish';
							else if($langCode == 'fr') $langStr = 'French';
							else if($langCode == 'pr') $langStr = 'Portuguese';
							echo '<div class="title-div"><span class="label">Title ('.$langStr.'):</span> '.$titleValue.'</div>';
						}
						echo '</div>';
					}
				}
				else echo 'No links have yet been defined';
				?>
			</div>
			<div class="field-block">
				<form name="resourceLinkForm" action="collmetadata.php" method="post" onsubmit="return verifyResourceLinkForm(this)">
					<div class="form-button">
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="action" type="hidden" value="saveResourceLink" />
						<input id="resourceJsonInput" name="resourcejson" type="hidden" value="<?php echo htmlspecialchars($collMetaArr['resourcejson']); ?>" />
					</div>
				</form>
			</div>
			<hr/>
			<fieldset style="width:90%;">
				<legend>Add/Edit External Link Resource</legend>
				<form name="linkForm" onsubmit="return false;">
					<div class="field-block" style="">
						<span class="field-label">URL:</span>
						<span class="field-elem"><input name="url" type="text" style="width:600px;" /></span>
					</div>
					<?php
					foreach($langArr as $langCode){
						$langStr = $langCode;
						if($langCode == 'en') $langStr = 'English';
						elseif($langCode == 'es') $langStr = 'Spanish';
						elseif($langCode == 'fr') $langStr = 'French';
						elseif($langCode == 'pr') $langStr = 'Portuguese';
						?>
						<div class="field-block" style="">
							<span class="field-label">Caption override (<?php echo $langStr; ?>):</span>
							<span class="field-elem">
								<input name="title-<?php echo $langCode; ?>" type="text" value="Homepage" />
							</span>
						</div>
						<?php
					}
					?>
					<div class="field-block" id="add-link-div">
						<span class="form-button"><button type="button" value="addLink" onclick="addLink(this.form);">Add Link</button></span>
					</div>
					<div class="field-block" id="edit-link-div" style="display: none">
						<span class="form-button"><button type="button" value="editLink" onclick="applyEdits(this.form);">Apply Edits</button></span>
						<input name="linkIndex" type="hidden" />
					</div>
				</form>
			</fieldset>
		</fieldset>
		<fieldset>
			<legend>Contacts</legend>
			<div id="contact-listing">
				<?php
				if($contactArr = json_decode($collMetaArr['contactjson'],true)){
					foreach($contactArr as $key => $valueArr){
						echo '<div class="contact-div">';
						echo '<div>'.$valueArr['firstName'].' '.$valueArr['lastName'];
						echo '<a href="#" onclick="editContact('.$key.');return false"><img src="../../images/edit.png" /></a>';
						?>
						<form name="contactDelForm" action="collmetadata.php" method="post" style="display:inline">
							<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
							<input name="contactIndex" type="hidden" value="<?php echo $key; ?>" />
							<input name="action" type="hidden" value="deleteContact" />
							<input type="image" src="../../images/del.png" style="width:13px" />
						</form>
						<?php
						echo '</div>';
						if(isset($valueArr['role'])) echo '<div style="margin-left:15px"><span class="label">Role: </span>'.$valueArr['role'].'</div>';
						if(isset($valueArr['email'])){
							echo '<div style="margin-left:15px">';
							echo '<span class="label">Email: </span>'.$valueArr['email'];
							if(isset($valueArr['centralContact'])) echo ' (central contact)';
							echo '</div>';
						}
						if(isset($valueArr['phone'])) echo '<div style="margin-left:15px"><span class="label">Phone: </span>'.$valueArr['phone'].'</div>';
						if(isset($valueArr['orcid'])){
							echo '<div style="margin-left:15px">';
							echo '<span class="label">ORCID #: </span><a href="https://orcid.org/'.$valueArr['orcid'].'" target="_blank">'.$valueArr['orcid'].'</a>';
							echo '</div>';
						}
						echo '</div>';
					}
				}
				else{
					echo 'No contacts have yet been defined';
				}
				?>
			</div>
			<hr/>
			<fieldset style="width:90%;">
				<legend>Add/Edit Contact</legend>
				<form name="contactEditForm" action="collmetadata.php" method="post" onsubmit="return verifyContactForm(this);">
					<div class="field-block">
						<span class="field-label">First name:</span>
						<span class="field-elem"><input name="firstName" type="text" required /></span>
					</div>
					<div class="field-block">
						<span class="field-label">Last name:</span>
						<span class="field-elem"><input name="lastName" type="text" required /></span>
					</div>
					<div class="field-block">
						<span class="field-label">Role:</span>
						<span class="field-elem"><input name="role" type="text" /></span>
					</div>
					<div class="field-block">
						<span class="field-label">Email:</span>
						<span class="field-elem"><input name="email" type="text" /></span>
					</div>
					<div class="field-block">
						<span class="field-elem"><input name="centralContact" type="checkbox" value="1" /></span>
						<span class="field-label">is central contact</span>
					</div>
					<div class="field-block">
						<span class="field-label">Phone:</span>
						<span class="field-elem"><input name="phone" type="text" /></span>
					</div>
					<div class="field-block">
						<span class="field-label">ORCID #:</span>
						<span class="field-elem"><input name="orcid" type="text" /></span>
					</div>
					<div class="field-block">
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="contactIndex" type="hidden" value="" />
						<span class="form-button"><button name="action" type="submit" value="saveContact"><span id="addContact-span">Add Contact</span><span id="editContact-span">Edit Contact</span></button></span>
						<span class="form-button"><button name="reset" type="reset" onclick="resetContactForm()">Reset Form</button></span>
					</div>
				</form>
			</fieldset>
		</fieldset>
		<fieldset>
			<legend>Mailing Address</legend>
			<?php
			if($instArr = $collManager->getAddress()){
				?>
				<div style="margin:25px;">
					<?php
					echo '<div>';
					echo $instArr['institutionname'].($instArr['institutioncode']?' ('.$instArr['institutioncode'].')':'');
					?>
					<a href="institutioneditor.php?emode=1&targetcollid=<?php echo $collid.'&iid='.$instArr['iid']; ?>" title="Edit institution address">
						<img src="../../images/edit.png" style="width:14px;" />
					</a>
					<a href="collmetadata.php?tabindex=1&collid=<?php echo $collid.'&removeiid='.$instArr['iid']; ?>" title="Unlink institution address">
						<img src="../../images/drop.png" style="width:14px;" />
					</a>
					<?php
					echo '</div>';
					if($instArr['address1']) echo '<div>'.$instArr['address1'].'</div>';
					if($instArr['address2']) echo '<div>'.$instArr['address2'].'</div>';
					if($instArr['city'] || $instArr['stateprovince']) echo '<div>'.$instArr['city'].', '.$instArr['stateprovince'].' '.$instArr['postalcode'].'</div>';
					if($instArr['country']) echo '<div>'.$instArr['country'].'</div>';
					if($instArr['phone']) echo '<div>'.$instArr['phone'].'</div>';
					if($instArr['contact']) echo '<div>'.$instArr['contact'].'</div>';
					if($instArr['email']) echo '<div>'.$instArr['email'].'</div>';
					if($instArr['url']) echo '<div><a href="'.$instArr['url'].'">'.$instArr['url'].'</a></div>';
					if($instArr['notes']) echo '<div>'.$instArr['notes'].'</div>';
					?>
				</div>
				<?php
			}
			else{
				//Link new institution
				?>
				<div style="margin:40px;"><b>No addesses linked</b></div>
				<div style="margin:20px;">
					<form name="addaddressform" action="collmetadata.php" method="post" onsubmit="return verifyAddAddressForm(this)">
						<select name="iid" style="width:425px;">
							<option value="">Select Institution Address</option>
							<option value="">------------------------------------</option>
							<?php
							$addrArr = $collManager->getInstitutionArr();
							foreach($addrArr as $iid => $name){
								echo '<option value="'.$iid.'">'.$name.'</option>';
							}
							?>
						</select>
						<input name="collid" type="hidden" value="<?php echo $collid; ?>" />
						<input name="action" type="submit" value="Link Address" />
					</form>
					<div style="margin:15px;">
						<a href="institutioneditor.php?emode=1&targetcollid=<?php echo $collid; ?>" title="Add a new address not on the list">
							<b>Add an institution not on list</b>
						</a>
					</div>
				</div>
				<?php
			}
			?>
		</fieldset>
	</div>
	<script type="text/javascript">
		//var resourceJSON = [{"title":{"en":"link1","es":"enlace1"},"url":"https:\/\/swbiodiversity.org\/seinet\/"},{"title":{"en":"link2","es":"enlace2"},"url":"https:\/\/swbiodiversity.org\/seinet2\/"}];
		var resourceJSON = <?php echo (isset($collMetaArr['resourcejson'])&&$collMetaArr['resourcejson']?$collMetaArr['resourcejson']:'[]'); ?>;
		var langArr = [<?php echo '"'.implode('","', $langArr).'"';?>];
		var contactJSON = <?php echo (isset($collMetaArr['contactjson'])&&$collMetaArr['contactjson']?$collMetaArr['contactjson']:'[]'); ?>;

		function addLink(f){
			var jsonObj = getFormObj(f);
			if(jsonObj) resourceJSON.push(jsonObj);
			submitResourceForm();
		}

		function editLink(linkIndex){
			var f = document.linkForm;
			clearForm();
			f.url.value = resourceJSON[linkIndex].url;
			for(var i = 0; i < langArr.length; i++) {
				try {
					var titleValue = resourceJSON[linkIndex].title[langArr[i]];
					if(titleValue != undefined) f["title-"+langArr[i]].value = titleValue;
				}
				catch(err) {}
			}
			f.linkIndex.value = linkIndex;
			$("#add-link-div").hide();
			$("#edit-link-div").show();
		}

		function applyEdits(f){
			linkIndex = f.linkIndex.value;
			if(linkIndex!=""){
				var f = document.linkForm;
				var jsonObj = getFormObj(f);
				if(jsonObj) resourceJSON[linkIndex] = jsonObj;
				submitResourceForm();
			}
		}

		function getFormObj(f){
			var jsonObj;
			if(f.url.value != ""){
				jsonObj = {"title":{},"url":f.url.value};
				for(var i = 0; i < langArr.length; i++) {
					if(f["title-"+langArr[i]] && f["title-"+langArr[i]].value){
						jsonObj.title[langArr[i]] = f["title-"+langArr[i]].value;
					}
				}
				clearForm();
			}
			else alert("Link ULR and title is required");
			return jsonObj;
		}

		function deleteLink(linkIndex){
			resourceJSON.splice(linkIndex,1);
			submitResourceForm();
		}

		function clearForm(){
			var f = document.linkForm;
			f.url.value = "";
			f.url.focus();
			for(var i = 0; i < langArr.length; i++) {
				f["title-"+langArr[i]].value = "";
			}
			$("#add-link-div").show();
			$("#edit-link-div").hide();
		}

		function submitResourceForm(){
			var f = document.resourceLinkForm
			f.resourcejson.value = JSON.stringify(resourceJSON);
			f.submit();
		}

		function editContact(contactIndex){
			var f = document.contactEditForm;
			f.contactIndex.value = contactIndex;
			f.firstName.value = contactJSON[contactIndex].firstName;
			f.lastName.value = contactJSON[contactIndex].lastName;
			if(contactJSON[contactIndex].role != undefined) f.role.value = contactJSON[contactIndex].role;
			else f.role.value = "";
			if(contactJSON[contactIndex].email != undefined) f.email.value = contactJSON[contactIndex].email;
			else f.email.value = "";
			if(contactJSON[contactIndex].centralContact != undefined) f.centralContact.checked = true;
			else f.centralContact.checked = false;
			if(contactJSON[contactIndex].phone != undefined) f.phone.value = contactJSON[contactIndex].phone;
			else f.phone.value = "";
			if(contactJSON[contactIndex].orcid != undefined) f.orcid.value = contactJSON[contactIndex].orcid;
			else f.orcid.value = "";
			$("#editContact-span").show();
			$("#addContact-span").hide();
		}

		function resetContactForm(){
			$("#editContact-span").hide();
			$("#addContact-span").show();
		}

		function verifyContactForm(f){
			if(f.firstName.value == ""){
				alert("First name is a required field");
				return false;
			}
			if(f.lastName.value == ""){
				alert("First name is a required field");
				return false;
			}
			return true;
		}
	</script>
	<?php
}
?>