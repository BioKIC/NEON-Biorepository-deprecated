<?php
include_once('../../config/symbini.php');
include_once($SERVER_ROOT.'/classes/OccurrenceCollectionProfile.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: ../../profile/index.php?refurl=../collections/misc/collmetadata.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$collid = array_key_exists('collid',$_REQUEST)?$_REQUEST['collid']:0;
$tabIndex = array_key_exists('tabindex',$_REQUEST)?$_REQUEST['tabindex']:0;
$action = array_key_exists('action',$_REQUEST)?$_REQUEST['action']:'';

if(!is_numeric($collid)) $collid = 0;
if(!is_numeric($tabIndex)) $tabIndex = 0;
if(preg_match('/[^a-zA-Z\s]+/', $action)) $action = '';

$isEditor = 0;
if($IS_ADMIN) $isEditor = 1;
elseif($collid){
	if(array_key_exists("CollAdmin",$USER_RIGHTS) && in_array($collid,$USER_RIGHTS['CollAdmin'])){
		$isEditor = 1;
	}
}

$collManager = new OccurrenceCollectionProfile($isEditor&&($action||!$collid)?'write':'readonly');
$collManager->setCollid($collid);

$statusStr = '';
if($isEditor){
	if($action == 'Save Edits'){
		$statusStr = $collManager->submitCollEdits($_POST);
		if($statusStr === true) header('Location: collprofiles.php?collid='.$collid);
	}
	elseif($action == 'Create New Collection'){
		if($IS_ADMIN){
			$newCollid = $collManager->submitCollAdd($_POST);
			if(is_numeric($newCollid)){
				$statusStr = 'New collection added successfully!<br/>Add contacts, resource links, or institution address below.<br/>Click <a href="../admin/specuploadmanagement.php?collid='.$newCollid.'&action=addprofile">here</a> to upload specimen records for this new collection.';
				$collid = $newCollid;
				$tabIndex = 1;
			}
			else{
				$statusStr = $collid;
			}
		}
	}
	elseif($action == 'saveResourceLink'){
		if(!$collManager->saveResourceLink($_POST)) $statusStr = $collManager->getErrorMessage();
		$tabIndex = 1;
	}
	elseif($action == 'saveContact'){
		if(!$collManager->saveContact($_POST)) $statusStr = $collManager->getErrorMessage();
		$tabIndex = 1;
	}
	elseif($action == 'deleteContact'){
		if(!$collManager->deleteContact($_POST['contactIndex'])) $statusStr = $collManager->getErrorMessage();
		$tabIndex = 1;
	}
	elseif($action == 'Link Address'){
		if(!$collManager->linkAddress($_POST['iid'])) $statusStr = $collManager->getErrorMessage();
	}
	elseif(array_key_exists('removeiid',$_GET)){
		if(!$collManager->removeAddress($_GET['removeiid'])) $statusStr = $collManager->getErrorMessage();
	}
}
$collData = current($collManager->getCollectionMetadata());
$collManager->cleanOutArr($collData);
?>
<html>
<head>
	<title><?php echo $DEFAULT_TITLE.' '.($collid?$collData['collectionname']:'') ; ?> Collection Profiles</title>
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
	?>
	<script src="../../js/jquery.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js" type="text/javascript"></script>
	<script src="../../js/symb/common.js" type="text/javascript"></script>
	<script>
		$(function() {
			var dialogArr = new Array("instcode","collcode","pedits","pubagg","rights","rightsholder","accessrights","guid","colltype","management","icon","collectionid","sourceurl","sort");
			var dialogStr = "";
			for(i=0;i<dialogArr.length;i++){
				dialogStr = dialogArr[i]+"info";
				$( "#"+dialogStr+"dialog" ).dialog({
					autoOpen: false,
					modal: true,
					position: { my: "left", at: "center", of: "#"+dialogStr }
				});
				$( "#"+dialogStr ).click(function() {
					$( "#"+this.id+"dialog" ).dialog( "open" );
				});
			}
			<?php
			if(isset($collData['contactjson'])){
				?>
				$('#tabs').tabs({
					select: function(event, ui) {
						return true;
					},
					active: <?php echo $tabIndex; ?>,
					beforeLoad: function( event, ui ) {
						$(ui.panel).html("<p>Loading...</p>");
					}
				});
				<?php
			}
			?>
		});

		function verifyCollEditForm(f){
			if(f.institutioncode.value == ''){
				alert("Institution Code must have a value");
				return false;
			}
			if(f.collectionname.value == ''){
				alert("Collection Name must have a value");
				return false;
			}
			if(f.managementtype.value == "Snapshot"){
				if(f.guidtarget.value == "symbiotaUUID"){
					alert("The Symbiota Generated GUID option cannot be selected for a collection that is managed locally outside of the data portal (e.g. Snapshot management type). In this case, the GUID must be generated within the source collection database and delivered to the data portal as part of the upload process.");
					return false;
				}
			}
			if(!isNumeric(f.latitudedecimal.value) || !isNumeric(f.longitudedecimal.value)){
				alert("Latitdue and longitude values must be in the decimal format (numeric only)");
				return false;
			}
			if(f.rights.value == ""){
				alert("Rights field (e.g. Creative Commons license) must have a selection");
				return false;
			}
			try{
				if(!isNumeric(f.sortseq.value)){
					alert("Sort sequence must be numeric only");
					return false;
				}
			}
			catch(ex){}
			return true;
		}

		function managementTypeChanged(selElem){
			if(selElem.value == "Live Data") $(".sourceurl-div").hide();
			else $(".sourceurl-div").show();
			checkManagementTypeGuidSource(selElem.form);
		}

		function checkManagementTypeGuidSource(f){
			if(f.managementtype.value == "Snapshot" && f.guidtarget.value == "symbiotaUUID"){
				alert("The Symbiota Generated GUID option cannot be selected for a collection that is managed locally outside of the data portal (e.g. Snapshot management type). In this case, the GUID must be generated within the source collection database and delivered to the data portal as part of the upload process.");
				f.guidtarget.value = '';
			}
			else if(f.managementtype.value == "Aggregate" && f.guidtarget.value != "" && f.guidtarget.value != "occurrenceId"){
				alert("An Aggregate dataset (e.g. specimens coming from multiple collections) can only have occurrenceID selected for the GUID source");
				f.guidtarget.value = 'occurrenceId';
			}
			if(!f.guidtarget.value) f.publishToGbif.checked = false;
		}

		function checkGUIDSource(f){
			if(f.publishToGbif.checked == true){
				if(!f.guidtarget.value){
					alert("You must select a GUID source in order to publish to data aggregators.");
					f.publishToGbif.checked = false;
				}
			}
		}

		function verifyAddAddressForm(f){
			if(f.iid.value == ""){
				alert("Select an institution to be linked");
				return false;
			}
			return true;
		}

		function verifyIconImage(f){
			var iconImageFile = document.getElementById("iconfile").value;
			if(iconImageFile){
				var iconExt = iconImageFile.substr(iconImageFile.length-4);
				iconExt = iconExt.toLowerCase();
				if((iconExt != '.jpg') && (iconExt != 'jpeg') && (iconExt != '.png') && (iconExt != '.gif')){
					document.getElementById("iconfile").value = '';
					alert("The file you have uploaded is not a supported image file. Please upload a jpg, png, or gif file.");
				}
				else{
					var fr = new FileReader;
					fr.onload = function(){
						var img = new Image;
						img.onload = function(){
							if((img.width>350) || (img.height>350)){
								document.getElementById("iconfile").value = '';
								img = '';
								alert("The image file must be less than 350 pixels in both width and height.");
							}
						};
						img.src = fr.result;
					};
					fr.readAsDataURL(document.getElementById("iconfile").files[0]);
				}
			}
		}

		function verifyIconURL(f){
			var iconImageFile = document.getElementById("iconurl").value;
			if(iconImageFile && (iconImageFile.substr(iconImageFile.length-4) != '.jpg') && (iconImageFile.substr(iconImageFile.length-4) != '.png') && (iconImageFile.substr(iconImageFile.length-4) != '.gif')){
				alert("The url you have entered is not for a supported image file. Please enter a url for a jpg, png, or gif file.");
			}
		}
	</script>
	<style type="text/css">
		fieldset { background-color: #f9f9f9; padding:15px }
		legend { font-weight: bold; }
		.field-block { margin: 5px 0px; }
		.field-label {  }
	</style>
</head>
<body>
	<?php
	$displayLeftMenu = (isset($collections_misc_collmetadataMenu)?$collections_misc_collmetadataMenu:true);
	include($SERVER_ROOT.'/includes/header.php');
	echo '<div class="navpath">';
	echo '<a href="../../index.php">Home</a> &gt;&gt; ';
	if($collid){
		echo '<a href="collprofiles.php?collid='.$collid.'&emode=1">Collection Management</a> &gt;&gt; ';
		echo '<b>'.$collData['collectionname'].' Metadata Editor</b>';
	}
	else echo '<b>Create New Collection Profile</b>';
	echo '</div>';
	?>
	<!-- This is inner text! -->
	<div id="innertext">
		<?php
		if($statusStr){
			$msgColor = 'red';
			if(stripos($msgColor,'success')) $msgColor = 'green';
			?>
			<hr />
			<div style="margin:20px;color:<?php echo $msgColor; ?>;">
				<?php echo $statusStr; ?>
			</div>
			<hr />
			<?php
		}
		?>
        <div id="tabs" style="margin:0px;">
			<?php
			if($isEditor){
				if($collid) echo '<h1>'.$collData['collectionname'].(array_key_exists('institutioncode',$collData)?' ('.$collData['institutioncode'].')':'').'</h1>';
				if(isset($collData['contactjson'])){
					?>
					<ul>
						<li><a href="#colleditor">Collection Metadata Editor</a></li>
						<li><a href="collmetaresources.php?collid=<?php echo $collid; ?>">Contacts & Resources</a></li>
					</ul>
					<?php
				}
				?>
				<div id="colleditor">
					<fieldset>
						<legend><?php echo ($collid?'Edit':'Add New'); ?> Collection Information</legend>
						<form id="colleditform" name="colleditform" action="collmetadata.php" method="post" enctype="multipart/form-data" onsubmit="return verifyCollEditForm(this)">
							<div class="field-block">
								<span class="field-label">Institution Code:</span>
								<span class="field-elem">
									<input type="text" name="institutioncode" value="<?php echo ($collid?$collData['institutioncode']:'');?>" />
									<a id="instcodeinfo" href="#" onclick="return false" title="More information about Institution Code">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="instcodeinfodialog">
										The name (or acronym) in use by the institution having custody of the occurrence records. This field is required.
										For more details, see <a href="http://rs.tdwg.org/dwc/terms/index.htm#institutionCode" target="_blank">Darwin Core definition</a>.
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label">Collection Code:</span>
								<span class="field-elem">
									<input type="text" name="collectioncode" value="<?php echo ($collid?$collData["collectioncode"]:'');?>" />
									<a id="collcodeinfo" href="#" onclick="return false" title="More information about Collection Code">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="collcodeinfodialog">
										The name, acronym, or code identifying the collection or data set from which the record was derived. This field is optional.
										For more details, see <a href="http://rs.tdwg.org/dwc/terms/index.htm#collectionCode" target="_blank">Darwin Core definition</a>.
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label">Collection Name:</span>
								<span class="field-elem">
									<input type="text" name="collectionname" value="<?php echo ($collid?$collData["collectionname"]:'');?>" style="width:600px;" title="Required field" />
								</span>
							</div>
							<div class="field-block">
								<span class="field-label">Description (2000 character max):</span>
								<div class="field-elem">
									<textarea name="fulldescription" style="width:95%;height:90px;"><?php echo ($collid?$collData["fulldescription"]:'');?></textarea>
								</div>
							</div>
							<?php
							if(!isset($collData['contactjson'])){
								?>
								<div id="url-div" class="field-block">
									<span class="field-label">Homepage:</span>
									<span class="field-elem">
										<input type="text" name="homepage" value="<?php echo $collData['homepage']; ?>" style="width:600px;" />
									</span>
								</div>
								<div id="contact-div" class="field-block">
									<span class="field-label">Contact:</span>
									<span class="field-elem">
										<input type="text" name="contact" value="<?php echo ($collid?$collData["contact"]:'');?>" style="width:600px;" />
									</span>
								</div>
								<div id="email-div" class="field-block">
									<span class="field-label">Email:</span>
									<span class="field-elem">
										<input type="text" name="email" value="<?php echo ($collid?$collData["email"]:'');?>" style="width:600px" />
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<span class="field-label">Latitude:</span>
								<span class="field-elem">
									<input id="decimallatitude" name="latitudedecimal" type="text" value="<?php echo ($collid?$collData["latitudedecimal"]:'');?>" />
									<a href="#" onclick="openPopup('../tools/mappointaid.php?errmode=0');return false;"><img src="../../images/world.png" style="width:12px;" /></a>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label">Longitude:</span>
								<span class="field-elem">
									<input id="decimallongitude" name="longitudedecimal" type="text" value="<?php echo ($collid?$collData["longitudedecimal"]:'');?>" />
								</span>
							</div>
							<?php
							$fullCatArr = $collManager->getCategoryArr();
							if($fullCatArr){
								?>
								<div class="field-block">
									<span class="field-label">Category:</span>
									<span class="field-elem">
										<select name="ccpk">
											<option value="">No Category</option>
											<option value="">-------------------------------------------</option>
											<?php
											$catArr = $collManager->getCollectionCategories();
											foreach($fullCatArr as $ccpk => $category){
												echo '<option value="'.$ccpk.'" '.($collid && array_key_exists($ccpk, $catArr)?'SELECTED':'').'>'.$category.'</option>';
											}
											?>
										</select>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<span class="field-label">Allow Public Edits:</span>
								<span class="field-elem">
									<input type="checkbox" name="publicedits" value="1" <?php echo ($collData && $collData['publicedits']?'CHECKED':''); ?> />
									<a id="peditsinfo" href="#" onclick="return false" title="More information about Public Edits">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="peditsinfodialog">
										Checking public edits will allow any user logged into the system to modify specimen records
										and resolve errors found within the collection. However, if the user does not have explicit
										authorization for the given collection, edits will not be applied until they are
										reviewed and approved by collection administrator.
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label">License:</span>
								<span class="field-elem">
									<?php
									if(isset($RIGHTS_TERMS)){
										?>
										<select name="rights">
											<?php
											$hasOrphanTerm = true;
											foreach($RIGHTS_TERMS as $k => $v){
												$selectedTerm = '';
												if($collid && strtolower($collData["rights"])==strtolower($v)){
													$selectedTerm = 'SELECTED';
													$hasOrphanTerm = false;
												}
												echo '<option value="'.$v.'" '.$selectedTerm.'>'.$k.'</option>'."\n";
											}
											if($hasOrphanTerm && array_key_exists("rights",$collData)){
												echo '<option value="'.$collData["rights"].'" SELECTED>'.$collData["rights"].' [orphaned term]</option>'."\n";
											}
											?>
										</select>
										<?php
									}
									else{
										?>
										<input type="text" name="rights" value="<?php echo ($collid?$collData["rights"]:'');?>" style="width:90%;" />
										<?php
									}
									?>
									<a id="rightsinfo" href="#" onclick="return false" title="More information about Rights">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="rightsinfodialog">
										A legal document giving official permission to do something with the resource.
										This field can be limited to a set of values by modifying the portal's central configuration file.
										For more details, see <a href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:license" target="_blank">Darwin Core definition</a>.
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label">Rights Holder:</span>
								<span class="field-elem">
									<input type="text" name="rightsholder" value="<?php echo ($collid?$collData["rightsholder"]:'');?>" style="width:600px" />
									<a id="rightsholderinfo" href="#" onclick="return false" title="More information about Rights Holder">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="rightsholderinfodialog">
										The organization or person managing or owning the rights of the resource.
										For more details, see <a href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:rightsHolder" target="_blank">Darwin Core definition</a>.
									</span>
								</span>
							</div>
							<div class="field-block">
								<span class="field-label">Access Rights:</span>
								<span class="field-elem">
									<input type="text" name="accessrights" value="<?php echo ($collid?$collData["accessrights"]:'');?>" style="width:600px" />
									<a id="accessrightsinfo" href="#" onclick="return false" title="More information about Access Rights">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="accessrightsinfodialog">
										Informations or a URL link to page with details explaining how one can use the data.
										See <a href="http://rs.tdwg.org/dwc/terms/index.htm#dcterms:accessRights" target="_blank">Darwin Core definition</a>.
									</span>
								</span>
							</div>
							<?php
							if($IS_ADMIN){
								?>
								<div class="field-block">
									<span class="field-label">Dataset Type:</span>
									<span class="field-elem">
										<select name="colltype">
											<option value="Preserved Specimens">Preserved Specimens</option>
											<option <?php echo ($collid && $collData["colltype"]=='Observations'?'SELECTED':''); ?> value="Observations">Observations</option>
											<option <?php echo ($collid && $collData["colltype"]=='General Observations'?'SELECTED':''); ?> value="General Observations">Personal Observation Management</option>
										</select>
										<a id="colltypeinfo" href="#" onclick="return false" title="More information about Collection Type">
											<img src="../../images/info.png" style="width:15px;" />
										</a>
										<span id="colltypeinfodialog">
											Preserved Specimens signify a collection type that contains physical samples that are available for inspection by researchers and taxonomic experts.
											Use Observations when the record is not based on a physical specimen.
											Personal Observation Management is a dataset where registered users
											can independently manage their own subset of records. Records entered into this dataset are explicitly linked to the user&apos;s profile
											and can only be edited by them. This type of collection
											is typically used by field researchers to manage their collection data and print labels
											prior to depositing the physical material within a collection. Even though personal collections
											are represented by a physical sample, they are classified as &quot;observations&quot; until the
											physical material is publicly available within a collection.
										</span>
									</span>
								</div>
								<div class="field-block">
									<span class="field-label">Management:</span>
									<span class="field-elem">
										<select name="managementtype" onchange="managementTypeChanged(this)">
											<option>Snapshot</option>
											<option <?php echo ($collid && $collData["managementtype"]=='Live Data'?'SELECTED':''); ?>>Live Data</option>
											<option <?php echo ($collid && $collData["managementtype"]=='Aggregate'?'SELECTED':''); ?>>Aggregate</option>
										</select>
										<a id="managementinfo" href="#" onclick="return false" title="More information about Management Type">
											<img src="../../images/info.png" style="width:15px;" />
										</a>
										<span id="managementinfodialog">
											Use Snapshot when there is a separate in-house database maintained in the collection and the dataset
											within the Symbiota portal is only a periodically updated snapshot of the central database.
											A Live dataset is when the data is managed directly within the portal and the central database is the portal data.
										</span>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<span class="field-label" title="Source of Global Unique Identifier">GUID source:</span>
								<span class="field-elem">
									<select name="guidtarget" onchange="checkManagementTypeGuidSource(this.form)">
										<option value="">Not defined</option>
										<option value="">-------------------</option>
										<option value="occurrenceId" <?php echo ($collid && $collData["guidtarget"]=='occurrenceId'?'SELECTED':''); ?>>Occurrence Id</option>
										<option value="catalogNumber" <?php echo ($collid && $collData["guidtarget"]=='catalogNumber'?'SELECTED':''); ?>>Catalog Number</option>
										<option value="symbiotaUUID" <?php echo ($collid && $collData["guidtarget"]=='symbiotaUUID'?'SELECTED':''); ?>>Symbiota Generated GUID (UUID)</option>
									</select>
									<a id="guidinfo" href="#" onclick="return false" title="More information about Global Unique Identifier">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="guidinfodialog">
										Occurrence Id is generally used for Snapshot datasets when a Global Unique Identifier (GUID) field
										is supplied by the source database (e.g. Specify database) and the GUID is mapped to the
										<a href="http://rs.tdwg.org/dwc/terms/index.htm#occurrenceID" target="_blank">occurrenceId</a> field.
										The use of the Occurrence Id as the GUID is not recommended for live datasets.
										Catalog Number can be used when the value within the catalog number field is globally unique.
										The Symbiota Generated GUID (UUID) option will trigger the Symbiota data portal to automatically
										generate UUID GUIDs for each record. This option is recommended for many for Live Datasets
										but not allowed for Snapshot collections that are managed in local management system.
									</span>
								</span>
							</div>
							<?php
							if(isset($GBIF_USERNAME) && isset($GBIF_PASSWORD) && isset($GBIF_ORG_KEY) && $GBIF_ORG_KEY) {
								?>
								<div class="field-block">
									<span class="field-label">Publish to Aggregators:</span>
									<span class="field-elem">
										GBIF <input type="checkbox" name="publishToGbif" value="1" onchange="checkGUIDSource(this.form);" <?php echo($collData['publishtogbif'] ? 'CHECKED' : ''); ?> />
										<a id="pubagginfo" href="#" onclick="return false"
										   title="More information about Publishing to Aggregators">
											<img src="../../images/info.png" style="width:15px;"/>
										</a>
										<!--
										<span>
											iDigBio <input type="checkbox" name="publishToIdigbio" value="1" onchange="checkGUIDSource(this.form);" <?php echo($collData['publishtoidigbio']?'CHECKED':''); ?> />
										</span>
										 -->
										<span id="pubagginfodialog">
											Activates GBIF publishing tools available within Darwin Core Archive Publishing menu option.
										</span>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<div class="sourceurl-div" style="display:<?php echo ($collData["managementtype"]=='Live Data'?'none':'');?>">
									<span class="field-label">Source Record URL:</span>
									<span class="field-elem">
										<input type="text" name="individualurl" style="width:700px" value="<?php echo ($collid?$collData["individualurl"]:'');?>" title="Dynamic link to source database individual record page" />
										<a id="sourceurlinfo" href="#" onclick="return false" title="More information about Source Records URL">
											<img src="../../images/info.png" style="width:15px;" />
										</a>
										<span id="sourceurlinfodialog">
											Advance setting: Adding a URL template here will insert a link to the source record within the specimen details page.
											A optional URL title can be include with a colon delimiting the title and URL.
											For example, &quot;SEINet source record:http://swbiodiversity.org/seinet/collections/individual/index.php?occid=--DBPK--&quot;
											will display the ID with the url pointing to the original record managed within SEINet. Or &quot;http://www.inaturalist.org/observations/--DBPK--&quot; can be used for an
											iNaturalist import if you mapped their ID field as the source Identifier (e.g. dbpk) during import.
											Template patterns --CATALOGNUMBER--, --OTHERCATALOGNUMBERS--, and --OCCURRENCEID-- are additional options.
										</span>
									</span>
								</div>
							</div>
							<div class="field-block">
								<span class="field-label">Icon URL:</span>
								<span class="field-elem">
									<span class="icon-elem" style="display:<?php echo (($collid&&$collData["icon"])?'none;':''); ?>">
										<input type='hidden' name='MAX_FILE_SIZE' value='20000000' />
										<input name='iconfile' id='iconfile' type='file' onchange="verifyIconImage(this.form);" />
									</span>
									<span class="icon-elem" style="display:<?php echo (($collid&&$collData["icon"])?'':'none'); ?>">
										<input style="width:600px;" type='text' name='iconurl' id='iconurl' value="<?php echo ($collid?$collData["icon"]:'');?>" onchange="verifyIconURL(this.form);" />
									</span>
									<a id="iconinfo" href="#" onclick="return false" title="What is an Icon?">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="iconinfodialog">
										Upload an icon image file or enter the URL of an image icon that represents the collection. If entering the URL of an image already located
										on a server, click on &quot;Enter URL&quot;. The URL path can be absolute or relative. The use of icons are optional.
									</span>
								</span>
								<span class="icon-elem" style="display:<?php echo (($collid&&$collData["icon"])?'none;':''); ?>">
									<a href="#" onclick="toggle('icon-elem');return false;">Enter URL</a>
								</span>
								<span class="icon-elem" style="display:<?php echo (($collid&&$collData["icon"])?'':'none;'); ?>">
									<a href="#" onclick="toggle('icon-elem');return false;">
										Upload Local Image
									</a>
								</span>
							</div>
							<?php
							if($IS_ADMIN){
								?>
								<div class="field-block" style="clear:both">
									<span class="field-label">Sort Sequence:</span>
									<span class="field-elem">
										<input type="text" name="sortseq" value="<?php echo ($collid?$collData["sortseq"]:'');?>" />
										<a id="sortinfo" href="#" onclick="return false" title="More information about Sorting">
											<img src="../../images/info.png" style="width:15px;" />
										</a>
										<span id="sortinfodialog">
											Leave this field empty if you want the collections to sort alphabetically (default)
										</span>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<span class="field-label">Collection ID (GUID):</span>
								<span class="field-elem">
									<input type="text" name="collectionid" value="<?php echo ($collid?$collData["collectionid"]:'');?>" style="width:400px" />
									<a id="collectionidinfo" href="#" onclick="return false" title="More information">
										<img src="../../images/info.png" style="width:15px;" />
									</a>
									<span id="collectionidinfodialog">
										Global Unique Identifier for this collection (see <a href="https://dwc.tdwg.org/terms/#dwc:collectionID" target="_blank">dwc:collectionID</a>):
										If your collection already has a previously assigned GUID, that identifier should be represented here.
										For physical specimens, the recommended best practice is to use an identifier from a collections registry such as the
										Global Registry of Biodiversity Repositories (<a href="http://grbio.org" target="_blank">http://grbio.org</a>).
									</span>
								</span>
							</div>
							<?php
							if($collid){
								?>
								<div class="field-block">
									<span class="field-label">Security Key:</span>
									<span class="field-elem">
										<?php echo $collData['skey']; ?>
									</span>
								</div>
								<div class="field-block">
									<span class="field-label">recordID:</span>
									<span class="field-elem">
										<?php echo $collData['recordid']; ?>
									</span>
								</div>
								<?php
							}
							?>
							<div class="field-block">
								<div style="margin:20px;">
									<?php
									if($collid){
										?>
										<input type="hidden" name="collid" value="<?php echo $collid;?>" />
										<input type="submit" name="action" value="Save Edits" />
										<?php
									}
									else{
										?>
										<input type="submit" name="action" value="Create New Collection" />
										<?php
									}
									?>
								</div>
							</div>
						</form>
					</fieldset>
				</div>
				<?php
				if(!isset($collData['contactjson'])){
					?>
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
								<a href="collmetadata.php?collid=<?php echo $collid.'&removeiid='.$instArr['iid']; ?>" title="Unlink institution address">
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
					<?php
				}
			}
			?>
		</div>
	</div>
	<?php
	include($SERVER_ROOT.'/includes/footer.php');
	?>
</body>
</html>