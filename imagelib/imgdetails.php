<?php
include_once('../config/symbini.php');
include_once($SERVER_ROOT.'/classes/ImageDetailManager.php');
header("Content-Type: text/html; charset=".$CHARSET);

$imgId = $_REQUEST["imgid"];
$action = array_key_exists("submitaction",$_REQUEST)?$_REQUEST["submitaction"]:"";
$eMode = array_key_exists("emode",$_REQUEST)?$_REQUEST["emode"]:0;

$imgManager = new ImageDetailManager($imgId,($action?'write':'readonly'));

$imgArr = $imgManager->getImageMetadata();
$isEditor = false;
if($IS_ADMIN || $imgArr["username"] === $USERNAME || ($imgArr["photographeruid"] && $imgArr["photographeruid"] == $SYMB_UID)){
    $isEditor = true;
}

$status = "";
if($isEditor){
	if($action == "Submit Image Edits"){
		$status = $imgManager->editImage($_POST);
		if(is_numeric($status)) header( 'Location: ../taxa/profile/tpeditor.php?tid='.$status.'&tabindex=1' );
	}
	elseif($action == "Transfer Image"){
		$imgManager->changeTaxon($_REQUEST["targettid"],$_REQUEST["sourcetid"]);
		header( 'Location: ../taxa/profile/tpeditor.php?tid='.$_REQUEST["targettid"].'&tabindex=1' );
	}
	elseif($action == "Delete Image"){
		$imgDel = $_REQUEST["imgid"];
		$removeImg = (array_key_exists("removeimg",$_REQUEST)?$_REQUEST["removeimg"]:0);
		$status = $imgManager->deleteImage($imgDel, $removeImg);
		if(is_numeric($status)){
			header( 'Location: ../taxa/profile/tpeditor.php?tid='.$status.'&tabindex=1' );
		}
	}
	$imgArr = $imgManager->getImageMetadata($imgId);
}

$serverPath = 'http://';
if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) $serverPath = 'https://';
$serverPath .= $_SERVER['SERVER_NAME'];
if($imgArr){
	$imgUrl = $imgArr["url"];
	$origUrl = $imgArr["originalurl"];
	$metaUrl = $imgArr["url"];
	if(array_key_exists("imageDomain",$GLOBALS)){
		if(substr($imgUrl,0,1)=="/"){
			$imgUrl = $GLOBALS["imageDomain"].$imgUrl;
			$metaUrl = $GLOBALS["imageDomain"].$metaUrl;
		}
		if($origUrl && substr($origUrl,0,1)=="/"){
			$origUrl = $GLOBALS["imageDomain"].$origUrl;
		}
	}
	if(substr($metaUrl,0,1)=="/"){
		$metaUrl = $serverPath.$metaUrl;
	}
}

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<?php
	if($imgArr){
		?>
		<meta property="og:title" content="<?php echo $imgArr["sciname"]; ?>"/>
		<meta property="og:site_name" content="<?php echo $DEFAULT_TITLE; ?>"/>
		<meta property="og:image" content="<?php echo $metaUrl; ?>"/>
		<meta name="twitter:card" content="photo" data-dynamic="true" />
		<meta name="twitter:title" content="<?php echo $imgArr["sciname"]; ?>" />
		<meta name="twitter:image" content="<?php echo $metaUrl; ?>" />
		<meta name="twitter:url" content="<?php echo $serverPath.$CLIENT_ROOT.'/imagelib/imgdetails.php?imgid='.$imgId; ?>" />
		<?php
	}
	?>
	<title><?php echo $DEFAULT_TITLE." Image Details: #".$imgId; ?></title>
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
	<script src="../js/jquery.js" type="text/javascript"></script>
	<script src="../js/jquery-ui.js" type="text/javascript"></script>
	<script src="../js/symb/shared.js" type="text/javascript"></script>
	<script src="../js/symb/api.taxonomy.taxasuggest.js?ver=3" type="text/javascript"></script>
</head>
<body>
	<div id="fb-root"></div>
	<script>
		(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));

		function verifyEditForm(f){
		    if(f.url.value.replace(/\s/g, "") == "" ){
		        window.alert("ERROR: File path must be entered");
		        return false;
		    }
		    return true;
		}

		function verifyChangeTaxonForm(f){
			var sciName = f.targettaxon.value.replace(/^\s+|\s+$/g, "");
		    if(sciName == ""){
		        window.alert("Enter a taxon name to which the image will be transferred");
		    }
			else{
				validateTaxon(f,true);
			}
		    return false;	//Submit takes place in the validateTaxon method
		}

		function openOccurrenceSearch(target) {
			occWindow=open("../collections/misc/occurrencesearch.php?targetid="+target,"occsearch","resizable=1,scrollbars=0,width=750,height=500,left=20,top=20");
			if (occWindow.opener == null) occWindow.opener = self;
		}
	</script>
	<?php
	$displayLeftMenu = (isset($taxa_imgdetailsMenu)?$taxa_imgdetailsMenu:false);
	include($SERVER_ROOT.'/includes/header.php');
	?>
	<div class="navpath">
		<a href="../index.php">Home</a> &gt;&gt;
		<a href="index.php">Image Browser</a> &gt;&gt;
		<a href="search.php">Image Search</a> &gt;&gt;
		<?php
		if(isset($imgArr['tid']) && $imgArr['tid']) echo '<a href="../taxa/index.php?tid='.$imgArr['tid'].'">Image Search</a> &gt;&gt;';
		echo '<b>Image Profile: image <a href="imgdetails.php?imgid='.$imgId.'">#'.$imgId.'</a></b>';
		?>
	</div>
	<div id="innertext">
		<!-- This is inner text! -->
		<?php
		if($imgArr){
			?>
			<div style="width:100%;float:right;clear:both;margin-top:10px;">
				<div style="float:right;">
					<a class="twitter-share-button" data-text="<?php echo $imgArr["sciname"]; ?>" href="https://twitter.com/share" data-url="<?php echo $_SERVER['HTTP_HOST'].$CLIENT_ROOT.'/imagelib/imgdetails.php?imgid='.$imgId; ?>">Tweet</a>
					<script>
						window.twttr=(function(d,s,id){
							var js,fjs=d.getElementsByTagName(s)[0],t=window.twttr||{};
							if(d.getElementById(id))return;js=d.createElement(s);
							js.id=id;js.src="https://platform.twitter.com/widgets.js";
							fjs.parentNode.insertBefore(js,fjs);t._e=[];
							t.ready=function(f){t._e.push(f);};
							return t;
						}(document,"script","twitter-wjs"));
					</script>
				</div>
				<div style="float:right;margin-right:10px;">
					<div class="fb-share-button" data-href="" data-layout="button_count"></div>
				</div>
			</div>
			<?php
		}
		if($status){
			?>
			<hr/>
			<div style="color:red;">
				<?php echo $status; ?>
			</div>
			<hr/>
			<?php
		}
		if($imgArr){
			if($isEditor){
				?>
				<div id="imageedit" style="display:<?php echo ($eMode?'block':'none'); ?>;">
					<form name="editform" action="imgdetails.php" method="post" target="_self" onsubmit="return verifyEditForm(this);">
						<fieldset style="margin:5px 0px 5px 5px;">
					    	<legend><b>Edit Image Details</b></legend>
					    	<div style="margin-top:2px;">
					    		<b>Caption:</b>
								<input name="caption" type="text" value="<?php echo $imgArr["caption"];?>" style="width:250px;" />
							</div>
							<div style="margin-top:2px;">
								<b>Photographer User ID:</b>
								<select name="photographeruid" name="photographeruid">
									<option value="">Select Photographer</option>
									<option value="">---------------------------------------</option>
									<?php $imgManager->echoPhotographerSelect($imgArr["photographeruid"]); ?>
								</select>
								* Users registered within system
								<a href="#" onclick="toggle('iepor');return false;" title="Display photographer override field">
									<img src="../images/editplus.png" style="border:0px;width:12px;" />
								</a>
							</div>
							<div id="iepor" style="margin-top:2px;display:<?php echo ($imgArr["photographer"]?'block':'none'); ?>;">
								<b>Photographer (override):</b>
								<input name="photographer" type="text" value="<?php echo $imgArr["photographer"];?>" style="width:250px;" />
								* Will override above selection
							</div>
							<div style="margin-top:2px;">
								<b>Manager:</b>
								<input name="owner" type="text" value="<?php echo $imgArr["owner"];?>" style="width:250px;" />
							</div>
							<div style="margin-top:2px;">
								<b>Source URL:</b>
								<input name="sourceurl" type="text" value="<?php echo $imgArr["sourceurl"];?>" style="width:450px;" />
							</div>
							<div style="margin-top:2px;">
								<b>Copyright:</b>
								<input name="copyright" type="text" value="<?php echo $imgArr["copyright"];?>" style="width:450px;" />
							</div>
							<div style="margin-top:2px;">
								<b>Rights:</b>
								<input name="rights" type="text" value="<?php echo $imgArr["rights"];?>" style="width:450px;" />
							</div>
							<div style="margin-top:2px;">
								<b>Locality:</b>
								<input name="locality" type="text" value="<?php echo $imgArr["locality"];?>" style="width:550px;" />
							</div>
							<div style="margin-top:2px;">
								<b>Occurrence Record #:</b>
								<input id="occid" name="occid" type="text" value="<?php  echo $imgArr["occid"];?>" />
								<span style="cursor:pointer;color:blue;"  onclick="openOccurrenceSearch('occid')">Link to Occurrence Record</span>
							</div>
							<div style="margin-top:2px;">
								<b>Notes:</b>
								<input name="notes" type="text" value="<?php echo $imgArr["notes"];?>" style="width:550px;" />
							</div>
							<div style="margin-top:2px;">
								<b>Sort sequence:</b>
								<input name="sortsequence" type="text" value="<?php echo $imgArr["sortsequence"];?>" size="5" />
							</div>
							<div style="margin-top:2px;">
								<b>Web Image:</b><br/>
								<input name="url" type="text" value="<?php echo $imgArr["url"];?>" style="width:90%;" />
								<?php if(stripos($imgArr["url"],$imageRootUrl) === 0){ ?>
								<div style="margin-left:70px;">
									<input type="checkbox" name="renameweburl" value="1" />
									Rename web image file on server to match above edit (web server file editing privileges required)
								</div>
								<input name="oldurl" type="hidden" value="<?php echo $imgArr["url"];?>" />
								<?php } ?>
							</div>
							<div style="margin-top:2px;">
								<b>Thumbnail:</b><br/>
								<input name="thumbnailurl" type="text" value="<?php echo $imgArr["thumbnailurl"];?>" style="width:90%;" />
								<?php if(stripos($imgArr["thumbnailurl"],$imageRootUrl) === 0){ ?>
								<div style="margin-left:70px;">
									<input type="checkbox" name="renametnurl" value="1" />
									Rename thumbnail image file on server to match above edit (web server file editing privileges required)
								</div>
								<input name="oldthumbnailurl" type="hidden" value="<?php echo $imgArr["thumbnailurl"];?>" />
								<?php } ?>
							</div>
							<div style="margin-top:2px;">
								<b>Large Image:</b><br/>
								<input name="originalurl" type="text" value="<?php echo $imgArr["originalurl"];?>" style="width:90%;" />
								<?php if(stripos($imgArr["originalurl"],$imageRootUrl) === 0){ ?>
								<div style="margin-left:80px;">
									<input type="checkbox" name="renameorigurl" value="1" />
									Rename large image file on server to match above edit (web server file editing privileges required)
								</div>
								<input name="oldoriginalurl" type="hidden" value="<?php echo $imgArr["originalurl"];?>" />
								<?php } ?>
							</div>
							<input name="imgid" type="hidden" value="<?php echo $imgId; ?>" />
							<div style="margin-top:2px;">
								<input type="submit" name="submitaction" id="editsubmit" value="Submit Image Edits" />
							</div>
						</fieldset>
					</form>
					<form name="changetaxonform" action="imgdetails.php" method="post" target="_self" onsubmit="return verifyChangeTaxonForm(this);" >
						<fieldset style="margin:5px 0px 5px 5px;">
					    	<legend><b>Transfer Image to a Different Scientific Name</b></legend>
							<div style="font-weight:bold;">
								Transfer to Taxon:
								<input type="text" id="taxa" name="targettaxon" size="40" />
								<input type="hidden" id="tid" name="targettid" value="" />
								<input type="hidden" name="sourcetid" value="<?php echo $imgArr["tid"];?>" />
								<input type="hidden" name="imgid" value="<?php echo $imgId; ?>" />
								<input type="hidden" name="submitaction" value="Transfer Image" />
								<input type="submit" name="submitbutton" value="Transfer Image" />
							</div>
					    </fieldset>
					</form>
					<form name="deleteform" action="imgdetails.php" method="post" target="_self" onsubmit="return window.confirm('Are you sure you want to delete this image? Note that the physical image will be deleted from the server if checkbox is selected.');">
						<fieldset style="margin:5px 0px 5px 5px;">
					    	<legend><b>Authorized to Remove this Image</b></legend>
							<input name="imgid" type="hidden" value="<?php echo $imgId; ?>" />
							<div style="margin-top:2px;">
								<input type="submit" name="submitaction" id="submit" value="Delete Image"/>
							</div>
							<input name="removeimg" type="checkbox" value="1" /> Remove image from server
							<div style="margin-left:20px;color:red;">
								(Note: if box is checked, image will be permanently deleted from server, as well as from database)
							</div>
				    	</fieldset>
				    </form>
				</div>
				<?php
			}
			?>
			<div>
				<div style="width:350px;padding:10px;float:left;">
					<?php
					if((!$imgUrl || $imgUrl == 'empty') && $origUrl) $imgUrl = $origUrl;
					?>
					<a href="<?php echo $imgUrl;?>">
						<img src="<?php echo $imgUrl;?>" style="width:300px;" />
					</a>
					<?php
					if($origUrl && $imgUrl != $origUrl) echo '<div><a href="'.$origUrl.'">Click on Image to Enlarge</a></div>';
					?>
				</div>
				<div style="padding:10px;float:left;">
					<?php
					if($SYMB_UID && ($IS_ADMIN || array_key_exists("TaxonProfile",$USER_RIGHTS))){
						?>
						<div style="float:right;margin-right:15px;" title="Go to Taxon Profile editing page">
							<a href="../taxa/profile/tpeditor.php?tid=<?php echo $imgArr['tid']; ?>&tabindex=1">
								<img src="../images/edit.png" style="border:0px;" /><span style="font-size:70%">TP</span>
							</a>
						</div>
						<?php
					}
					if($imgArr['occid']){
						?>
						<div style="float:right;margin-right:15px;" title="Must have editing privileges for this collection managing image">
							<a href="../collections/editor/occurrenceeditor.php?occid=<?php echo $imgArr['occid']; ?>&tabtarget=2">
								<img src="../images/edit.png" style="border:0px;" /><span style="font-size:70%">SPEC</span>
							</a>
						</div>
						<?php
					}
					else{
						if($isEditor){
							?>
							<div style="float:right;margin-right:15px;">
								<a href="#" onclick="toggle('imageedit');return false" title="Edit Image">
									<img src="../images/edit.png" style="border:0px;" /><span style="font-size:70%">IMG</span>
								</a>
							</div>
							<?php
						}
					}
					?>
					<div style="clear:both;margin-top:80px;">
						<b>Scientific Name:</b> <?php echo '<a href="../taxa/index.php?taxon='.$imgArr["tid"].'"><i>'.$imgArr["sciname"].'</i> '.$imgArr["author"].'</a>'; ?>
					</div>
					<?php
					if($imgArr['caption']) echo '<div><b>Caption:</b> '.$imgArr['caption'].'</div>';
					if($imgArr['photographerdisplay']){
						echo '<div><b>Photographer:</b> ';
						if(!$imgArr['photographer']){
							$phLink = 'search.php?imagetype=all&phuid='.$imgArr['photographeruid'].'&submitaction=search';
							echo '<a href="'.$phLink.'">';
						}
						echo $imgArr['photographerdisplay'];
						if(!$imgArr['photographer']) echo '</a>';
						echo '</div>';
					}
					if($imgArr['owner']) echo '<div><b>Manager:</b> '.$imgArr['owner'].'</div>';
					if($imgArr['sourceurl']) echo '<div><b>Image Source:</b> <a href="'.$imgArr['sourceurl'].'" target="_blank">'.$imgArr['sourceurl'].'</a></div>';
					if($imgArr['locality']) echo '<div><b>Locality:</b> '.$imgArr['locality'].'</div>';
					if($imgArr['notes']) echo '<div><b>Notes:</b> '.$imgArr['notes'].'</div>';
					if($imgArr['rights']){
						echo '<div><b>Rights:</b> '.$imgArr['rights'].'</div>';
					}
					if($imgArr['copyright']){
						echo '<div>';
						echo '<b>Copyright:</b> ';
						if(stripos($imgArr['copyright'],'http') === 0) echo '<a href="'.$imgArr['copyright'].'">'.$imgArr['copyright'].'</a>';
						else echo $imgArr['copyright'];
						echo '</div>';
					}
					else{
						echo '<div><a href="../includes/usagepolicy.php#images">Copyright Details</a></div>';
					}
					if($imgArr['occid']) echo '<div><a href="../collections/individual/index.php?occid='.$imgArr['occid'].'">Display Specimen Details</a></div>';
					if($imgUrl) echo '<div><a href="'.$imgUrl.'">Open Medium Sized Image</a></div>';
					if($origUrl) echo '<div><a href="'.$origUrl.'">Open Large Image</a></div>';
					?>
					<div style="margin-top:20px;">
						Do you see an error or have a comment about this image? <br/>If so, send email to:
						<?php
						$emailSubject = $DEFAULT_TITLE.' Image #'.$imgId;
						$emailBody = 'Image being referenced: '.$serverPath.$CLIENT_ROOT.'/imagelib/imgdetails.php?imgid='.$imgId;
						$emailRef = 'subject='.$emailSubject.'&cc='.$adminEmail.'&body='.$emailBody;
						?>
						<a href="mailto:<?php echo $adminEmail.'?'.$emailRef; ?>">
							<?php echo $adminEmail; ?>
						</a>

					</div>
				</div>
			</div>
			<?php
		}
		else{
			echo '<h2 style="margin:30px;">Unable to locate image.</h2>';
		}
		?>
	</div>
<?php
include($SERVER_ROOT.'/includes/footer.php');
?>
</body>
</html>