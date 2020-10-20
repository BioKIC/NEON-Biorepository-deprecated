<?php
/*
 * Image maintenance scripts:
 *   1) Navigates through submitted image ids (imgid) and removes image records from database and deletes or moves physical image to an archive directory
 *   2) Script that assists in migrating images from a remote server to the portal mount
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');
include_once('../../../config/symbini.php');

$collid = (array_key_exists('collid', $_POST)?$_POST['collid']:'');
$submit = (array_key_exists('submitbutton', $_POST)?$_POST['submitbutton']:'');

//Sanitation
if(!is_numeric($collid)) $collid = '';

$toolManager = new MediaTools();
$toolManager->setCollid($collid);

$isEditor = false;
if($IS_ADMIN) $isEditor = true;
?>
<html>
<head>
	<title>Media Tools</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $CHARSET; ?>"/>
	<?php
	$activateJQuery = true;
	include_once($SERVER_ROOT.'/includes/head.php');
	?>
	<script src="../../js/jquery.js" type="text/javascript"></script>
	<script src="../../js/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript">
	</script>
	<style type="text/css">
		fieldset{ padding: 10px }
		legend{ font-weight: bold }
		.fieldRowDiv{ clear:both; margin: 2px 0px; }
		.fieldDiv{ float:left; margin: 2px 10px 2px 0px; }
		.fieldLabel{ font-weight: bold; display: block; }
		.fieldDiv button{ margin-top: 10px; }
	</style>
</head>
<body>
	<?php
	if($isEditor){
		?>
		<div id="actionDiv">
			<?php
			if($submit){
				if($submit == 'transferImages'){
					?>
					<fieldset>
						<legend>Action Panel</legend>
						<ol>
						<?php
						$transferThumbnail = (array_key_exists('transferThumbnail', $_POST)?$_POST['transferThumbnail']:0);
						$transferWeb = (array_key_exists('transferWeb', $_POST)?$_POST['transferWeb']:0);
						$transferLarge = (array_key_exists('transferLarge', $_POST)?$_POST['transferLarge']:0);
						$matchTermThumbnail = (array_key_exists('matchTermThumbnail', $_POST)?$_POST['matchTermThumbnail']:'');
						$matchTermWeb = (array_key_exists('matchTermWeb', $_POST)?$_POST['matchTermWeb']:'');
						$matchTermLarge = (array_key_exists('matchTermLarge', $_POST)?$_POST['matchTermLarge']:'');
						$imgRootUrl = (array_key_exists('imgRootUrl', $_POST)?$_POST['imgRootUrl']:'');
						$imgRootPath = (array_key_exists('imgRootPath', $_POST)?$_POST['imgRootPath']:'');
						$imgSubPath = (array_key_exists('imgSubPath', $_POST)?$_POST['imgSubPath']:'');

						//Sanitation
						if(!is_numeric($transferThumbnail)) $transferThumbnail = 0;
						if(!is_numeric($transferWeb)) $transferWeb = 0;
						if(!is_numeric($transferLarge)) $transferLarge = 0;
						$matchTermThumbnail = filter_var($matchTermThumbnail,FILTER_SANITIZE_STRING);
						$matchTermWeb = filter_var($matchTermWeb,FILTER_SANITIZE_STRING);
						$matchTermLarge = filter_var($matchTermLarge,FILTER_SANITIZE_STRING);
						$imgRootUrl = filter_var($imgRootUrl,FILTER_SANITIZE_STRING);
						$imgRootPath = filter_var($imgRootPath,FILTER_SANITIZE_STRING);
						$imgSubPath = filter_var($imgSubPath,FILTER_SANITIZE_STRING);

						$toolManager->setTransferThumbnail($transferThumbnail);
						$toolManager->setTransferWeb($transferWeb);
						$toolManager->setTransferLarge($transferLarge);
						$toolManager->setMatchTermThumbnail($matchTermThumbnail);
						$toolManager->setMatchTermWeb($matchTermWeb);
						$toolManager->setMatchTermLarge($matchTermLarge);
						$toolManager->setImgRootUrl($imgRootUrl);
						$toolManager->setImgRootPath($imgRootPath);
						$toolManager->setImgSubPath($imgSubPath);
						$toolManager->migrateDerivatives($limit);
						?>
						</ol>
					</fieldset>
					<?php
				}
				elseif($submit == 'Process Images'){
					$imgidStart = (array_key_exists('imgidstart', $_POST)?$_POST['imgidstart']:0);
					$limit = (array_key_exists('limit', $_POST)?$_POST['limit']:10000);
					$archiveImages = (array_key_exists('archiveimg', $_POST)?$_POST['archiveimg']:0);
					$delThumb = (array_key_exists('delthumb', $_POST)?$_POST['delthumb']:0);
					$delWeb = (array_key_exists('delweb', $_POST)?$_POST['delweb']:0);
					$delLarge = (array_key_exists('dellarge', $_POST)?$_POST['dellarge']:0);
					$imgidStr = (array_key_exists('imgidstr', $_POST)?$_POST['imgidstr']:'');

					//Sanitation
					if(!is_numeric($imgidStart)) $imgidStart = 0;
					if(!is_numeric($limit)) $limit = 0;
					if(!is_numeric($archiveImages)) $archiveImages = 0;
					if(!is_numeric($delThumb)) $delThumb = 0;
					if(!is_numeric($delWeb)) $delWeb = 0;
					if(!is_numeric($delLarge)) $delLarge = 0;
					if(!is_numeric($imgidStr)) $imgidStr = 0;

					$imgidEnd = 0;

					if($archiveImages) $toolManager->setArchiveImages($archiveImages);
					$toolManager->setDeleteThumbnail($delThumb);
					$toolManager->setDeleteWebImage($delWeb);
					$toolManager->setDeleteOriginal($delLarge);
					$toolManager->setImgidArr($imgidStr);
					$imgidEnd = $toolManager->archiveImageFiles($imgidStart, $limit);
				}
				else{
					$delThumb = 1;
					$delWeb = 1;
					$delLarge = 1;
				}
			}
			?>
		</div>
		<form action="media_scripts.php" method="post">
			<div class="fieldRowDiv">
				<div class="fieldDiv">
					<span class="fieldLabel">Collection ID (collid):</span>
					<select name="collid">
						<option value="">Select a Collection</option>
						<option value="">-----------------------------</option>
						<option value="0">Field Images</option>
						<?php
						$collArr = $toolManager->getCollectionMeta();
						foreach($collArr as $id => $collName){
							echo '<option value="'.$id.'" '.($collid==$id?'SELECTED':'').'>'.$collName.'</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div class="fieldRowDiv">
				<div class="fieldDiv">
					<b>Starting Image ID:</b> <input type="text" name="imgidstart" value="<?php echo $imgidEnd; ?>" /><br />
				</div>
			</div>
			<div class="fieldRowDiv">
				<div class="fieldDiv">
					<b>Batch limit: </b><input type="text" name="limit" value="<?php echo $limit; ?>" /><br />
				</div>
			</div>
			<div class="fieldRowDiv">
				<div class="fieldDiv">
					<fieldset>
						<legend>Action</legend>
						<input type="radio" name="archiveimg" value="0" <?php echo ($archiveImages?'':'CHECKED'); ?> /> Delete Images<br />
						<input type="radio" name="archiveimg" value="1" <?php echo ($archiveImages?'CHECKED':''); ?> /> Archive Images<br />
					</fieldset>
				</div>
			</div>
			<div class="fieldRowDiv">
				<div class="fieldDiv">
					<fieldset>
						<legend>Image Targets</legend>
						<input type="checkbox" name="delthumb" value="1" <?php echo ($delThumb?'CHECKED':''); ?> /> Delete Thumbnail Derivative<br />
						<input type="checkbox" name="delweb" value="1" <?php echo ($delWeb?'CHECKED':''); ?> /> Delete Web Derivative<br />
						<input type="checkbox" name="dellarge" value="1" <?php echo ($delLarge?'CHECKED':''); ?> /> Delete Large Derivative<br />
					</fieldset>
				</div>
			</div>
			<div class="fieldRowDiv">
				<div class="fieldDiv">
					<b>imgids (enter multiple values delimited by commas)</b><br/>
					<textarea name="imgidstr" rows="8" cols="100"></textarea>
				</div>
			</div>
			<div class="fieldRowDiv">
				<div class="fieldDiv">
					<button name="submitbutton" type="submit" value="Process Images">Process Images</button>
				</div>
			</div>
		</form>
		<form action="media_scripts.php" method="post">
			<div class="fieldRowDiv">
				<div class="fieldDiv">
					<span class="fieldLabel">Collection ID (collid):</span>
					<select name="collid">
						<option value="">Select a Collection</option>
						<option value="">-----------------------------</option>
						<option value="0">Field Images</option>
						<?php
						$collArr = $toolManager->getCollectionMeta();
						foreach($collArr as $id => $collName){
							echo '<option value="'.$id.'" '.($collid==$id?'SELECTED':'').'>'.$collName.'</option>';
						}
						?>
					</select>
				</div>
			</div>
			<fieldset>
				<legend>Transfer Target</legend>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<input name="transferThumbnail" type="checkbox" value="1" <?php echo ($transferThumbnail?'CHECKED':''); ?> />
						<span class="fieldLabel">Transfer Thumbnail</span>
					</div>
				</div>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<input name="transferWed" type="checkbox" value="1" <?php echo ($transferWeb?'CHECKED':''); ?> />
						<span class="fieldLabel">Transfer Web View (medium)</span>
					</div>
				</div>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<input name="transferLarge" type="checkbox" value="1" <?php echo ($transferLarge?'CHECKED':''); ?> />
						<span class="fieldLabel">Transfer Large Image</span>
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>Transfer Source Query Term</legend>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<span class="fieldLabel">Thumbnail Matching Term (thumbnailUrl):</span>
						<input name="matchTermThumbnail" type="text" value="<?php echo $matchTermThumbnail; ?>" />
					</div>
				</div>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<span class="fieldLabel">Web Image (medium) Matching Term (url):</span>
						<input name="matchTermWeb" type="text" value="<?php echo $matchTermWeb; ?>" />
					</div>
				</div>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<span class="fieldLabel">Large Image Matching Term (originalurl):</span>
						<input name="matchTermLarge" type="text" value="<?php echo $matchTermLarge; ?>" />
					</div>
				</div>
			</fieldset>
			<fieldset>
				<legend>Path Variables</legend>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<span class="fieldLabel">Image Root URL (imgRootUrl):</span>
						<input name="imgRootUrl" type="text" value="<?php echo ($imgRootUrl?$imgRootUrl:$IMAGE_ROOT_URL); ?>" />
					</div>
				</div>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<span class="fieldLabel">Image Root Path (imgRootPath):</span>
						<input name="imgRootPath" type="text" value="<?php echo ($imgRootPath?$imgRootPath:$IMAGE_ROOT_PATH); ?>" />
					</div>
				</div>
				<div class="fieldRowDiv">
					<div class="fieldDiv">
						<span class="fieldLabel">Target Sub-Path:</span>
						<input name="imgSubPath" type="text" value="<?php echo $imgSubPath; ?>" />
					</div>
				</div>
			</fieldset>
			<div class="fieldRowDiv">
				<div class="fieldDiv">
					<span class="fieldLabel">Batch limit:</span>
					<input type="text" name="limit" value="<?php echo $limit; ?>" />
				</div>
			</div>
			<div class="fieldRowDiv">
				<button name="submitbutton" type="submit" value="transferImages">Transfer Images</button>
			</div>
		</form>
		<?php
	}
	else{
		echo '<div>Permissions issue; are you logged in?</div>';
	}
	?>
</body>

<?php
include_once($SERVER_ROOT.'/classes/Manager.php');
class MediaTools extends Manager {

	private $collid;
	private $reportFH;

	//Archiver variables
	private $imgidArr;
	private $archiveImages = false;
	private $archiveDir;
	private $deleteThumbnail = false;
	private $deleteWeb = false;
	private $deleteOriginal = false;

	//Image migration variables
	private $collid;
	private $collMetaArr;
	private $transferThumbnail = false;
	private $transferWeb = false;
	private $transferLarge = false;
	private $matchTermThumbnail;
	private $matchTermWeb;
	private $matchTermLarge;
	private $imgRootUrl;
	private $imgRootPath;
	private $imgSubPath;

	function __construct() {
		parent::__construct('write');
		set_time_limit(600);
		$this->verboseMode = 3;
		$this->setLogFH('../../../temp/logs/imgMigration_error_'.date('Ym').'.log');
		$this->reportFH = fopen('../../../temp/logs/imgMigration_'.date('Ym').'.log', 'a');
	}

	function __destruct(){
		parent::__destruct();
		fclose($this->reportFH);
	}

	//Archiver functions
	public function archiveImageFiles($imgidStart, $limit){
		//Set stage
		if(!$imgidStart) $imgidStart = 0;
		if(!$this->imgidArr){
			echo '<li>ABORTED: Image ids (imgid) not supplied</li>';
			return false;
		}
		$this->archiveDir = $GLOBALS['IMAGE_ROOT_PATH'].'/archive_'.date('Y-m-d');
		if(!file_exists($this->archiveDir)){
			if(!mkdir($this->archiveDir)) {
				echo '<li>ABORTED: unalbe to create archive directory ('.$this->archiveDir.')</li>';
				return false;
			}
		}
		$createHeader = true;
		if(file_exists($this->archiveDir.'/mediaArchiveReport.csv')) $createHeader = false;
		$this->reportFH = fopen($this->archiveDir.'/mediaArchiveReport.csv', 'a');
		if(!$this->reportFH){
			echo '<li>ABORTED: unalbe to create archive file ('.$this->archiveDir.')</li>';
			return false;
		}
		if($createHeader) fputcsv($this->reportFH, array('imgid','status','path','url'));
		//Remove images
		$imgidFinal = $imgidStart;
		$cnt = 0;
		$sql = 'SELECT i.* FROM images i ';
		if($this->collid) $sql .= 'INNER JOIN omoccurrences o ON i.occid = o.occid ';
		$sql .= 'WHERE (i.imgid IN('.trim(implode(',',$this->imgidArr),', ').')) AND (i.imgid > '.$imgidStart.') ';
		if($this->collid) $sql .= 'AND (o.collid = '.$this->collid.') ';
		$sql .= 'ORDER BY i.imgid LIMIT '.$limit;
		//echo $sql;
		$rs = $this->conn->query($sql);
		echo '<ul>';
		while($r = $rs->fetch_assoc()){
			$imgId = $r['imgid'];
			$derivArr = array('tn'=>1,'web'=>1,'lg'=>1);
			$delArr = array();
			if(!$r['thumbnailurl']) unset($derivArr['tn']);
			if(!$r['url']) unset($derivArr['web']);
			if(!$r['originalurl']) unset($derivArr['lg']);
			//Transfer images to archive folder
			if($this->deleteThumbnail && isset($derivArr['tn'])){
				if($this->archiveImage($r['thumbnailurl'], $imgId)){
					$delArr['tn'] = 1;
					unset($derivArr['tn']);
				}
			}
			if($this->deleteWeb && isset($derivArr['web'])){
				if($this->archiveImage($r['url'], $imgId)){
					$delArr['web'] = 1;
					unset($derivArr['web']);
				}
			}
			if($this->deleteOriginal && isset($derivArr['lg'])){
				if($this->archiveImage($r['originalurl'], $imgId)){
					$delArr['lg'] = 1;
					unset($derivArr['lg']);
				}
			}
			//Place INSERT sql into file in case record needs to be reintalled
			$insertArr = $r;
			unset($insertArr['imgid']);
			unset($insertArr['initialtimestamp']);
			$insertStr = '';
			foreach($insertArr as $v){
				if($v){
					$insertStr .= ',"'.$v.'"';
				}
				else{
					$insertStr .= ',NULL';
				}
			}
			$insSql = 'INSERT INTO images('.implode(',', array_keys($insertArr)).') VALUES('.substr($insertStr,1).');';
			fputcsv($this->reportFH,array($imgId,'record deleted',$insSql));
			//Adjust database record
			$sqlImg = '';
			if($derivArr){
				if(isset($delArr['tn'])) $sqlImg .= ', thumbnailurl = NULL';
				if(isset($delArr['web'])) $sqlImg .= ', url = "empty"';
				if(isset($delArr['lg'])) $sqlImg .= ', originalurl = NULL';
				if($sqlImg) $sqlImg = 'UPDATE images SET '.substr($sqlImg,1).' WHERE imgid = '.$imgId;
			}
			else{
				$sqlImg = 'DELETE FROM images WHERE imgid = '.$imgId;
			}
			if($sqlImg){
				if(!$this->conn->query($sqlImg)){
					echo '<li>ERROR: '.$this->conn->error.'</li>';
					echo '<li style="margin-left:15px;">sqlImg: '.$sqlImg.'</li>';
				}
			}
			if($cnt && $cnt%100 == 0){
				echo '<li>'.$cnt.' images checked</li>';
				ob_flush();
				flush();
			}
			$cnt++;
			$imgidFinal = $imgId;
		}
		echo '</ul>';
		$rs->free();
		fclose($this->reportFH);
		echo '<div>Done! '.$cnt.' images handled</div>';
		return $imgidFinal;
	}

	private function archiveImage($imgFilePath, $imgid){
		$status = false;
		if($imgFilePath){
			if(substr($imgFilePath,0,4) == 'http') {
				$imgFilePath = substr($imgFilePath,strpos($imgFilePath,"/",9));
			}
			$path = str_replace($GLOBALS['IMAGE_ROOT_URL'], $GLOBALS['IMAGE_ROOT_PATH'], $imgFilePath);
			if(is_writable($path)){
				if($this->archiveImages){
					$fileName = substr($path, strrpos($path, '/'));
					if(rename($path,$this->archiveDir.'/'.$fileName)) $status = true;
				}
				else{
					if(unlink($path)) $status = true;
				}
			}
			else{
				fputcsv($this->reportFH,array($imgid,'unwritable',$imgFilePath,$path));
				echo '<li>ERROR: image unwritable (imgid: <a href="'.$GLOBALS['CLIENT_ROOT'].'/imagelib/imgdetails.php?imgid='.$imgid.'" target="_blank">'.$imgid.'</a>, path: '.$imgFilePath.')</li>';
			}
		}
		return $status;
	}

	//Image migration functions
	public function migrateDerivatives($limit){
		if(is_numeric($limit) && is_numeric($this->collid) && $this->imgRootUrl && $this->imgRootPath){
			if($this->transferThumbnail && $this->transferWeb && $this->transferLarge){
				if($this->matchTermTn || $this->matchTermWeb || $this->matchTermLarge){
					$this->setTargetPaths();
					$dirCnt = 0;
					do{
						$imgArr = array();
						$pathFrag = date('Ym');
						if(!file_exists($this->imgRootPath.$pathFrag)) mkdir($this->imgRootPath.$pathFrag);
						$subDir = str_pad($dirCnt,4,'0',STR_PAD_LEFT);
						while(file_exists($this->imgRootPath.$pathFrag.'/'.$subDir)){
							$dirCnt ++;
							$subDir = str_pad($dirCnt,4,'0',STR_PAD_LEFT);
						}
						$pathFrag .= '/'.$subDir;
						$dirCnt ++;
						$sql = 'SELECT imgid, thumbnailurl, url, originalurl FROM images WHERE occid IS NULL ';
						if($this->collid) $sql = 'SELECT i.thumbnailurl, i.url, i.originalurl FROM images i INNER JOIN omoccurrences o ON i.occid = o.occid WHERE o.collid = '.$this->collid;
						if($this->matchTermThumbnail) $sql .= ' AND thumbnailurl LIKE "'.$this->matchTermThumbnail.'%" ';
						if($this->matchTermWeb) $sql .= ' AND url LIKE "'.$this->matchTermWeb.'%" ';
						if($this->matchTermLarge) $sql .= ' AND originalurl LIKE "'.$this->matchTermLarge.'%" ';
						$sql .= 'LIMIT 1000';
						$rs = $this->conn->query($sql);
						while($r = $rs->fetch_object()){
							if($this->transferThumbnail){
								$filePath = $pathFrag.strrpos($r->thumbnailurl+1, '/');
								if(copy($r->thumbnailurl,$this->imgRootPath.$filePath)){
									$imgArr[$r->imgid]['tn'] = $filePath;
									fwrite($this->reportFH,$r->thumbnailurl."\n");
								}
							}
							if($this->transferWeb){
								$filePath = $pathFrag.strrpos($r->url+1, '/');
								if(copy($r->url,$this->imgRootPath.$filePath)){
									$imgArr[$r->imgid]['web'] = $filePath;
									fwrite($this->reportFH,$r->url."\n");
								}
							}
							if($this->transferLarge){
								$filePath = $pathFrag.strrpos($r->originalurl+1, '/');
								if(copy($r->originalurl,$this->imgRootPath.$filePath)){
									$imgArr[$r->imgid]['lg'] = $filePath;
									fwrite($this->reportFH,$r->originalurl."\n");
								}
							}
							$limit--;
							if($limit < 1) break;
						}
						$rs->free();
						$this->processImageArr($imgArr);
						$cnt = count($imgArr);
						$this->logOrEcho($cnt.' image records remapped');
						unset($imgArr);
					}while($cnt && $limit);
				}
			}
		}
	}

	private function processImageArr($imgArr){
		foreach($imgArr as $imgID => $iArr){
			$sqlFrag = '';
			if(isset($iArr['tn'])) $sqlFrag .= 'thumbnailurl = "'.$this->imgRootUrl.$iArr['tn'].'"';
			if(isset($iArr['web'])) $sqlFrag .= ',url = "'.$this->imgRootUrl.$iArr['web'].'"';
			if(isset($iArr['lg'])) $sqlFrag .= ',originalurl = "'.$this->imgRootUrl.$iArr['lg'].'"';
			if($sqlFrag){
				$sql = 'UPDATE images '.trim($sqlFrag,' ,').' WHERE imgid = '.$imgID;
				if(!$this->conn->query($sql)) $this->logOrEcho('ERROR saving new paths: '.$this->conn->error,1);

			}
		}
	}

	private function setTargetPaths(){
		if($this->imgRootPath && $this->imgRootUrl){
			if($this->collid){
				$this->imgRootPath .= $this->collMetaArr['code'].'/';
			}
			elseif($this->collid === 0){
				$this->imgRootPath .= 'fieldimg/';
			}
			if(!file_exists($this->imgRootPath)) mkdir($this->imgRootPath);
		}
	}

	//Navigates through iDigBio media links and fixes bad full derivative links that were the result of a disk crash
	public function checkImageLinks($imgidStart, $limit, $collid){
		$imgidFinal = $imgidStart;
		$cnt = 1;
		$sql = 'SELECT i.imgid, i.originalurl FROM images i ';
		if($collid) $sql .= 'INNER JOIN omoccurrences o ON i.occid = o.occid ';
		$sql .= 'WHERE (i.originalurl LIKE "https://api.idigbio.org/v2/media/%size=fullsize") AND (i.imgid > '.$imgidStart.') ';
		if($collid) $sql .= 'AND (o.collid = '.$collid.') ';
		$sql .= 'ORDER BY i.imgid LIMIT '.$limit;
		$rs = $this->conn->query($sql);
		while($r = $rs->fetch_object()){
			$url = $r->originalurl;
			if($this->isBrokenUrl($url)){
				if($newUrl = substr($url,0,-14)){
					if(!$this->isBrokenUrl($newUrl)){
						$sql2 = 'UPDATE images SET originalurl = "'.$newUrl.'" WHERE imgid = '.$r->imgid;
						$this->conn->query($sql2);
						echo '<li>'.$cnt.': Remapping image #'.$r->imgid.' to: '.$newUrl.'</li>';
						ob_flush();
						flush();
					}
				}
			}
			//echo '<li>Image is good (imgid: '.$r->imgid.'): '.$url.'</li>';
			if($cnt%500 == 0){
				echo '<li>'.$cnt.' image checked (imgid: '.$r->imgid.')</li>';
				ob_flush();
				flush();
			}
			$cnt++;
			$imgidFinal = $r->imgid;
		}
		$rs->free();
		return $imgidFinal;
	}

	private function isBrokenUrl($url){
		$status = false;
		$handle = curl_init($url);
		if(false === $handle){
			$status = true;
		}
		curl_setopt($handle, CURLOPT_HEADER, true);
		curl_setopt($handle, CURLOPT_NOBODY, true);
		curl_setopt($handle, CURLOPT_FAILONERROR, true);
		curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true );
		//curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36');
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_exec($handle);
		$retCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		//print_r(curl_getinfo($handle));
		if($retCode == 403) $status = true;
		curl_close($handle);
		return $status;
	}

	//Misc data return functions
	public function getCollectionMeta(){
		$retArr = array();
		$sql = 'SELECT collid, collectionname, CONCAT_WS(":",institutioncode,collectioncode) as instcode FROM omcollections ORDER BY collectionname';
		$rs = $this->conn->fetch_object($sql);
		while($r = $rs->fetch_object()){
			$retArr[$r->collid]= $r->collectionname.' ('.$r->instcode.')';
		}
		$rs->free();
		return $retArr;
	}

	//Setters and getters
	public function setCollid($id){
		if(is_numeric($id)){
			$this->collid = $id;
			$sql = 'SELECT collectionname, CONCAT_WS("_",institutioncode,collectioncode) as instcode FROM omcollections WHERE collid = '.$id;
			$rs = $this->conn->fetch_object($sql);
			while($r = $rs->fetch_object()){
				$this->collMetaArr['name']= $r->collectionname;
				$this->collMetaArr['code']= $r->instcode;
			}
			$rs->free();
		}
	}

	//Archiver setters and getters
	public function setImgidArr($imgidStr){
		$imgidStr = str_replace(';', ' ', $imgidStr);
		$imgidStr = str_replace(',', ' ', $imgidStr);
		$imgidStr = trim(preg_replace('/\s\s+/',' ',$imgidStr),',');
		if($imgidStr){
			if(preg_match('/^[\d\s]+$/',$imgidStr)){
				$this->imgidArr = explode(' ',$imgidStr);
			}
		}
	}

	public function setArchiveImages($b){
		if($b) $this->archiveImages = true;
	}

	public function setDeleteThumbnail($delTn){
		if($delTn) $this->deleteThumbnail = true;
		else $this->deleteThumbnail = false;
	}

	public function setDeleteWebImage($delWeb){
		if($delWeb) $this->deleteWeb = true;
		else $this->deleteWeb = false;
	}

	public function setDeleteOriginal($delOrig){
		if($delOrig) $this->deleteOriginal = true;
		else $this->deleteOriginal = false;
	}

	//Image migration setters and getter
	public function setTransferThumbnail($bool){
		if($bool) $this->transferThumbnail = true;
		else $this->transferThumbnail = false;
	}

	public function setTransferWeb($bool){
		if($bool) $this->transferWeb = true;
		else $this->transferWeb = false;
	}

	public function setTransferLarge($bool){
		if($bool) $this->transferLarge = true;
		else $this->transferLarge = false;
	}

	public function setMatchTermThumbnail($str){
		$this->matchTermThumbnail = $str;
	}

	public function setMatchTermWeb($str){
		$this->matchTermWeb = $str;
	}

	public function setMatchTermLarge($str){
		$this->matchTermLarge = $str;
	}

	public function setImgRootUrl($url){
		if(substr($url, -1) != '/') $url .= '/';
		$this->imgRootUrl = $url;
	}

	public function setImgRootPath($url){
		if(substr($url, -1) != '/') $url .= '/';
		$this->imgRootPath = $url;
	}

	public function setImgSubPath($path){
		$this->imgSubPath = $path;
	}
}
?>