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
<!DOCTYPE html>
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
		//var linkJSON = [{"title":{"en":"link1","es":"enlace1"},"url":"https:\/\/swbiodiversity.org\/seinet\/"},{"title":{"en":"link2","es":"enlace2"},"url":"https:\/\/swbiodiversity.org\/seinet2\/"}];
		var linkJSON = [];
		var dataChanged = false;
		var langArr = [<?php echo '"'.implode('","', $langArr).'"';?>];

		$(document).ready(function() {
			var inStr = opener.document.colleditform.homepage.value;
			if(inStr.substring(0,1)=="[") linkJSON = JSON.parse(inStr);
			else linkJSON = [{"title":{},"url":inStr}];
			displayLinkObj();
		});

		function addLink(f){
			var jsonObj = getFormObj(f);
			if(jsonObj) linkJSON.push(jsonObj);
			displayLinkObj();
			dataChanged = true;
		}

		function editLink(linkIndex){
			var f = document.linkForm;
			clearForm();
			f.url.value = linkJSON[linkIndex].url;
			for(var i = 0; i < langArr.length; i++) {
				try {
					var titleValue = linkJSON[linkIndex].title[langArr[i]];
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
				if(jsonObj) linkJSON[linkIndex] = jsonObj;
				displayLinkObj();
				dataChanged = true;
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

		function pushLinksToEditor(){
			if(linkJSON){
				try{
					if(linkJSON.length > 1 || (linkJSON[0].title && linkJSON[0].title.en)){
						opener.document.colleditform.homepage.value = JSON.stringify(linkJSON);
						opener.document.colleditform.homepage.readonly = true;
					}
					else if(linkJSON[0].url){
						opener.document.colleditform.homepage.value = linkJSON[0].url;
						opener.document.colleditform.homepage.readonly = false;
					}
				}
				catch(err){}
			}
			self.close();
		}

		function deleteLink(linkIndex){
			linkJSON.splice(linkIndex,1);
			displayLinkObj();
			clearForm();
			dataChanged = true;
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

		function displayLinkObj(){
			if(linkJSON){
				$( "#link-listing-div" ).html("");
				$.each(linkJSON, function(key, linkObj){
					if(linkObj){
						var linkDiv = document.createElement("div");
						linkDiv.setAttribute("class","link-div");
						var urlAnchor = document.createElement("a");
						urlAnchor.setAttribute("href",linkObj.url);
						urlAnchor.setAttribute("target","_blank");
						editAnchor = document.createElement("a");
						editAnchor.setAttribute("onclick","editLink("+key+");return false");
						editAnchor.setAttribute("href","#");
						editImg = document.createElement("img");
						editImg.setAttribute("src","../../images/edit.png");
						editAnchor.appendChild(editImg);
						delAnchor = document.createElement("span");
						delAnchor.setAttribute("onclick","deleteLink("+key+");return false");
						delImg = document.createElement("img");
						delImg.setAttribute("src","../../images/del.png");
						delAnchor.appendChild(delImg);
						var urlText = document.createTextNode(linkObj.url);
						urlAnchor.appendChild(urlText);
						var urlDiv = document.createElement("div");
						urlDiv.appendChild(urlAnchor);
						urlDiv.appendChild(editAnchor);
						urlDiv.appendChild(delAnchor);
						linkDiv.appendChild(urlDiv);
						$.each(linkObj.title, function(langCode, titleValue){
							langStr = langCode;
							if(langCode == 'en') langStr = 'English';
							else if(langCode == 'es') langStr = 'Spanish';
							else if(langCode == 'fr') langStr = 'French';
							else if(langCode == 'pr') langStr = 'Portuguese';
							var titleDiv = document.createElement("div");
							titleDiv.setAttribute("class","title-div");
							titleText = document.createTextNode("Title ("+langStr+"): "+titleValue);
							titleDiv.appendChild(titleText);
							linkDiv.appendChild(titleDiv);
						});
						$( "#link-listing-div" ).append(linkDiv);
					}
				});
			}
		}

	</script>
	<style>
		window{ width:200px;height:500px;}
		body{ background-color:white; width:600px; min-width:600px; height:500px; }
		fieldset{ padding:15px; }
		legend{ font-weight:bold; }
		.link-div{ margin:10px 0px; }
		.link-div a{ margin-right:10px; }
		.link-div img{ width:13px; }
		.title-div{ margin-left: 10px; }
	</style>
</head>
<body>
	<!-- This is inner text! -->
	<div>
		<fieldset>
			<legend>Link Resource Listing</legend>
			<div id="link-listing-div">No links have yet been defined</div>
			<div class="field-block" id="push-link-div">
				<span class="form-button"><button type="button" value="submitLink" onclick="pushLinksToEditor();">Push Links to Editor</button></span>
			</div>
		</fieldset>
		<hr/>
		<fieldset style="width:450px;">
			<legend>Add New External Link Resource</legend>
			<form name="linkForm" onsubmit="return false;">
				<div class="field-block" style="">
					<span class="field-label">URL:</span>
					<span class="field-elem"><input name="url" type="text" style="width:350px;" /></span>
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
						<span class="field-label">Optional short caption (<?php echo $langStr; ?>):</span>
						<span class="field-elem">
							<input name="title-<?php echo $langCode; ?>" type="text" />
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
	</div>
</body>
</html>