<?php
include_once('../../config/symbini.php');
header("Content-Type: text/html; charset=".$CHARSET);

$langStr = 'en';
if(isset($DEFAULT_LANG)){
	$defaultLang = strtolower($DEFAULT_LANG);
	if($defaultLang == 'es') $langStr = 'es';
	elseif($defaultLang == 'fr') $langStr = 'fr';
	elseif($defaultLang == 'br') $langStr = 'br';
}
if(isset($EXTENDED_LANG) && $EXTENDED_LANG) $defaultLang = $EXTENDED_LANG;
$langArr = explode(',',$defaultLang);
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>">
	<title>Collection Multiple Resource Aid</title>
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
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery-ui.js"></script>
	<script type="text/javascript">
		var linkJSON = [{"title":{"en":"link1","es":"enlace1"},"url":"https:\/\/swbiodiversity.org\/seinet\/"},{"title":{"en":"link2","es":"enlace2"},"url":"https:\/\/swbiodiversity.org\/seinet2\/"}];
		var dataChanged = false;
		var langArr = [<?php echo '"'.implode('","', $langArr).'"';?>];

		$(document).ready(function() {
			linkJson = opener.document.contact.value;
			displayLinkObj();
		});

		function addLink(f){
			if(f.link.value != ""){
				var newJSON = {"title":{},"url":f.link.value};
				for(var i = 0; i < langArr.length; i++) {
					if(f[langArr[i]] && f["title-"+langArr[i]].value){
						newJSON.title[langArr[i]] = f[langArr[i]].value;
					}
				}
		    	//opener.document.fullform.associatedtaxa.value = asStr + nameElem.value;
		    	f.title.value = "";
		    	f.link.value = "";
		    	f.lang.value = "";
		    	f.title.focus();
			}
			else alert("Link ULR and title is required");
	    }

	    function editLink(f){
			var editIndex = f.editIndex.value;
			if(editIndex!=""){
				linkJSON[editIndex].url = f.title.url;
				for(var i = 0; i < langArr.length; i++) {
					if(f[langArr[i]] && f[langArr[i]].value){
						linkJSON[editIndex].title[langArr[i]] = f[langArr[i]].value;
					}
				}
			}
	    }

	    function deleteLink(linkIndex){
	    	delete linkJSON[linkIndex];
	    }

		function displayLinkObj(){
			if(linkJSON){
				$.each(linkJSON, function(key, linkObj){
					var newDiv = document.createElement("div");
					var newAnchor = document.createElement("a");
					var newText = document.createTextNode();
					var newAnchor = document.createElement("a");
					newAnchor.setAttribute("href","#");
					newAnchor.setAttribute("onclick","openIndividual("+occid+");return false;");
					newAnchor.appendChild(newText);
					newDiv.appendChild(newAnchor);

					alert("title: "+linkObj.title+"; link: "+linkObj.url);



					var newDiv = document.createElement("div");
					var newInput = document.createElement('input');
					newInput.setAttribute("name", "occidAssoc");
					newInput.setAttribute("type", "radio");
					newInput.setAttribute("value", occid);
					newDiv.appendChild(newInput);
					var newText = document.createTextNode(catnum+": "+collinfo);
					var newAnchor = document.createElement("a");
					newAnchor.setAttribute("href","#");
					newAnchor.setAttribute("onclick","openIndividual("+occid+");return false;");
					newAnchor.appendChild(newText);
					newDiv.appendChild(newAnchor);

				});
			}
		}

	</script>
	<style>
	</style>
</head>
<body style="background-color:white">
	<!-- This is inner text! -->
	<div id="" style="width:600px">
		<fieldset>
			<legend>Link Resource Listing</legend>
			<div id="link-listing-div">No links have yet been defined</div>
		</fieldset>
		<hr/>
		<fieldset style="width:450px;">
			<legend>Add New External Link Resource</legend>
			<form name="link-form" onsubmit="return false;">
				<div class="field-block" style="">
					<span class="field-label">URL:</span>
					<span class="field-elem"><input name="link" type="text" style="width:350px;" /></span>
				</div>
				<?php
				foreach($langArr as $langStr){
					?>
					<div class="field-block" style="">
						<span class="field-label">Short title (<?php echo $langStr; ?>):</span>
						<span class="field-elem">
							<input name="title-<?php echo $langStr; ?>" type="text" />
						</span>
					</div>
					<?php
				}
				?>
				<div class="field-block" style="">
					<span class="field-label">Short title:</span>
					<span class="field-elem"><input name="title" type="text" /></span>
				</div>
				<?php
				if(isset($EXTENDED_LANG) && $EXTENDED_LANG){
					?>
					<div class="field-block" style="">
						<span class="field-label">Title language:</span>
						<span class="field-elem">
							<select name="lang">
								<option value="">Select Language</option>
								<option value="">----------------</option>
								<?php
								$langArr = explode(',',$EXTENDED_LANG);
								foreach($langArr as $langStr){
									echo '<option value="'.$langStr.'">'.$langStr.'</option>';
								}
								?>
							</select>
						</span>
					</div>
					<?php
				}
				?>
				<div class="field-block" id="add-link-div">
					<span class="form-button"><button type="button" value="addLink" onclick="addLink();">Add Link</button></span>
				</div>
				<div class="field-block" id="edit-link-div" style="display: none">
					<span class="form-button"><button type="button" value="editLink" onclick="editLink();">Edit Link</button></span>
					<input name="editIndex" type="hidden" value="" />
				</div>
				<div class="field-block" id="add-link-div">
					<span class="form-button"><button type="button" value="submitLink" onclick="submitLink();">Add Link</button></span>
				</div>
			</form>
		</fieldset>
	</div>
</body>
</html>