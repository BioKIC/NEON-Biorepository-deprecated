<?php
require_once('../config/symbini.php');
require_once($SERVER_ROOT.'/classes/PortalIndex.php');
//include_once($SERVER_ROOT.'/content/lang/admin/portalindex.'.$LANG_TAG.'.php');
header("Content-Type: text/html; charset=".$CHARSET);

if(!$SYMB_UID) header('Location: '.$CLIENT_ROOT.'/profile/index.php?refurl=../admin/portalindex.php?'.htmlspecialchars($_SERVER['QUERY_STRING'], ENT_QUOTES));

$portalID = array_key_exists('portalid',$_REQUEST)?$_REQUEST['portalid']:0;
$remoteID = array_key_exists('remoteid',$_REQUEST)?$_REQUEST['remoteid']:0;
$formSubmit = array_key_exists('formsubmit',$_POST)?$_POST['formsubmit']:'';

//Sanitation
if(!is_numeric($portalID)) $portalID = 0;
if(!is_numeric($remoteID)) $remoteID = 0;

$portalManager = new PortalIndex();

$isEditor = 0;
if($IS_ADMIN) $isEditor = 1;
?>
<html>
	<head>
		<title><?php echo $DEFAULT_TITLE; ?> Portal Index Control Panel</title>
		<?php
		$activateJQuery = true;
		include_once($SERVER_ROOT.'/includes/head.php');
		?>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery.js" type="text/javascript"></script>
		<script src="<?php echo $CLIENT_ROOT; ?>/js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript">
		</script>
		<style type="text/css">
			fieldset{ margin:20px; padding:15px; }
			legend{ font-weight: bold; }
			label{  }
			button{ margin: 20px; }
			hr{ margin-top: 15px; margin-bottom: 15px; }
		</style>
	</head>
	<body>
		<?php
		$displayLeftMenu = true;
		include($SERVER_ROOT.'/includes/header.php');
		?>
		<div class="navpath">
			<a href="../../index.php">Home</a> &gt;&gt;
			<b><a href="portalindex.php">Portal Index Control Panel</a></b>
		</div>
		<div id="innertext">
			<?php
			if($isEditor){
				if($formSubmit == 'importProfile'){
					if($collid = $portalManager->importProfile($portalID, $remoteID)) echo '<div><a href="../collections/misc/collprofiles.php?collid='.$collid.'" target="_blank">New snapshot collection created</a></div>';
					else echo '<div>failed to insert new collections: '.$portalManager->getErrorMessage().'</div>';
				}
				$indexArr = $portalManager->getPortalIndexArr($portalID);
				?>
				<fieldset>
					<legend>Portal Index</legend>
					<?php
					foreach($indexArr as $portalID => $portalArr){
						foreach($portalArr as $fieldName => $fieldValue){
							if($fieldValue){
								echo '<div><label>'.$fieldName.'</label>: ';
								$href = '';
								if($fieldName=='urlRoot') $href = $fieldValue;
								elseif($fieldName=='guid') $href = $portalArr['urlRoot'].'/api/v2/installation/ping';
								if($href) echo '<a href="'.$href.'" target="_blank">';
								echo $fieldValue;
								if($href) echo '</a>';
								echo '</div>';
							}
						}
						if($remoteID){
							$collectArr = $portalManager->getCollectionList($portalArr['urlRoot'], $remoteID);
							echo '<fieldset>';
							echo '<legend>Remote Collection #'.$remoteID.'</legend>';
							$remoteCollid = $collectArr['collID'];
							unset($collectArr['collID']);
							unset($collectArr['iid']);
							$internalArr = $collectArr['internal'];
							unset($collectArr['internal']);
							foreach($collectArr as $fName => $fValue){
								if($fValue){
									if($fName == 'fullDescription') $fValue = htmlentities($fValue);
									echo '<div><label>'.$fName.'</label>: '.$fValue.'</div>';
								}
							}
							$remoteUrl = $portalArr['urlRoot'].'/collections/misc/collprofiles.php?collid='.$remoteCollid;
							echo '<div><label>Remote collection</label>: <a href="'.$remoteUrl.'" target="_blank">'.$remoteUrl.'</a></div>';
							if($internalArr){
								echo '<fieldset>';
								echo '<legend>Internally Mapped Snapshot Collection</legend>';
								foreach($internalArr as $collid => $intArr){
									echo '<div><label>Management Type</label>: '.$intArr['managementType'].'</div>';
									echo '<div><label>Specimen count</label>: '.number_format($intArr['recordCnt']).'</div>';
									echo '<div><label>Refresh date</label>: '.$intArr['uploadDate'].'</div>';
									$internalUrl = $CLIENT_ROOT.'/collections/misc/collprofiles.php?collid='.$collid;
									echo '<div><label>Internal collection</label>: <a href="'.$internalUrl.'" target="_blank">'.$internalUrl.'</a></div>';
									if($importProfile = $portalManager->getDataImportProfile($collid)){
										foreach($importProfile as $uspid => $profileArr){
											echo '<hr/>';
											echo '<div style="margin:10px 5px">';
											echo '<div><label>Title</label>: '.$profileArr['title'].'</div>';
											echo '<div><label>Path</label>: '.$profileArr['path'].'</div>';
											echo '<div><label>Query string</label>: '.$profileArr['queryStr'].'</div>';
											echo '<div><label>Stored procedure (cleaning)</label>: '.$profileArr['cleanUpSp'].'</div>';
											echo '<div>Go to <a href="../collections/admin/specuploadmap.php?uploadtype=9&uspid='.$uspid.'&collid='.$collid.'" target="_blank">Import Profile</a></div>';
											echo '</div>';
										}
									}
								}
								echo '</fieldset>';
							}
							else{
								?>
								<div style="margin: 0px 30px">
									<form name="collPubForm" method="post" action="portalindex.php">
										<input name="portalid" type="hidden" value="<?php echo $portalID; ?>" />
										<input name="remoteid" type="hidden" value="<?php echo $remoteID; ?>" />
										<button name="formsubmit" type="submit" value="importProfile">Create Internal Snapshot Profile</button>
									</form>
								</div>
								<?php
							}
							echo '</fieldset>';
						}
						elseif($formSubmit == 'listCollections'){
							if($collList = $portalManager->getCollectionList($portalArr['urlRoot'])){
								echo '<div><label>Collection Count</label>: '.count($collList).'</div>';
								echo '<table class="styledtable">';
								echo '<tr><th>ID</th><th>Institution Code</th><th>Collection Code</th><th>Collection Name</th><th>Dataset Type</th><th>Management</th><th>Mapped Internally</th></tr>';
								foreach($collList as $collArr){
									echo '<tr>';
									echo '<td><a href="portalindex.php?portalid='.$portalID.'&remoteid='.$collArr['collID'].'">'.$collArr['collID'].'</a></td>';
									echo '<td>'.$collArr['institutionCode'].'</td>';
									echo '<td>'.$collArr['collectionCode'].'</td>';
									echo '<td>'.$collArr['collectionName'].'</td>';
									echo '<td>'.$collArr['collType'].'</td>';
									echo '<td>'.$collArr['managementType'].'</td>';
									if(isset($collArr['internal']) && $collArr['internal'])
										$internal = '<a href="'.$CLIENT_ROOT.'/collections/misc/collprofiles.php?collid='.key($collArr['internal']).'" target="_blank">Yes</a>';
									else $internal = 'No';
									echo '<td>'.$internal.'</td>';
									echo '</tr>';
								}
								echo '</table>';
							}
						}
						else{
							?>
							<div style="margin:15px;">
								<form name="portalActionForm" method="post" action="portalindex.php">
									<input name="portalid" type="hidden" value="<?php echo $portalID; ?>" />
									<button name="formsubmit" type="submit" value="listCollections">List Collections</button>
								</form>
							</div>
							<?php
						}
						echo '<hr/>';
					}
					?>
				</fieldset>
				<?php
			}
			else echo '<h2>ERROR: access denied</h2>';
			?>
		</div>
		<?php
		include($SERVER_ROOT.'/includes/footer.php');
		?>
	</body>
</html>