<?php
$targetPath = '';
$recursive = true;
$degree = 90;
$rotateMode = 1;
$msgMode = 1;

if(isset($_REQUEST['path']) && $_REQUEST['path']) $targetPath = $_REQUEST['path'];
if(isset($_REQUEST['recursive']) && !$_REQUEST['recursive']) $recursive = false;
if(isset($_REQUEST['degree']) && is_numeric($_REQUEST['degree'])) $degree = $_REQUEST['degree'];
if(isset($_REQUEST['rotmode']) && is_numeric($_REQUEST['rotmode'])) $rotateMode = $_REQUEST['rotmode'];
if(isset($_REQUEST['msgmode']) && is_numeric($_REQUEST['msgmode'])) $msgMode = $_REQUEST['msgmode'];

$rotateManager = new ImageRotator();

$rotateManager->setRecursive($recursive);
$rotateManager->setDegree($degree);
$rotateManager->setRotateMode($rotateMode);
$rotateManager->setMsgOutMode($msgMode);

if($targetPath) $rotateManager->batchRotateImages($targetPath);
else echo 'ERROR: target path is empty';

class ImageRotator{
	private $targetPath = '';
	private $recursive = true;
	private $degree = 90;
	private $rotateMode = 1;		//1 = php, 2 = jpegtran, 3 = ImageMagick
	private $msgOutMode = 1;		//1 = text, 2 = html

	function __construct() {
	}

	function __destruct(){
	}

	public function batchRotateImages($targetPath){
		if($targetPath){
			if($fh = opendir($targetPath)){
				if($this->msgOutMode == 2) echo '<ul>';
				$this->msgOut('Stating directory: '.$targetPath);
				while (false !== ($entry = readdir($fh))){
					if($entry != "." && $entry != ".."){
						if(is_file($targetPath.'/'.$entry)){
							$this->msgOut('Evaluating: '.$targetPath.'/'.$entry);
							if(pathinfo($targetPath.'/'.$entry,PATHINFO_EXTENSION ) == 'jpg'){
								$imgInfoArr = getimagesize($targetPath.'/'.$entry);
								$ratio = $imgInfoArr[0]/$imgInfoArr[1];
								if($ratio > 1){
									$this->msgOut('Rotating...',1);
									$this->rotateImage($targetPath.'/'.$entry);
								}
								else $this->msgOut('Skipping ('.$ratio.')',1);
							}
							else $this->msgOut('ERROR: not a jpg',1);
						}
						elseif(is_dir($targetPath.'/'.$entry)){
							if($this->recursive) $this->batchRotateImages($targetPath.'/'.$entry);
						}
					}
				}
				if($this->msgOutMode == 2) echo '</ul>';
				closedir($fh);
			}
		}
	}

	private function rotateImage($imgPath){
		$status = false;
		if($this->rotateMode == 1) $status = $this->phpRotateJpeg($imgPath);
		elseif($this->rotateMode == 2) $status = $this->jpegtranRotate($imgPath);
		elseif($this->rotateMode == 3) $status = $this->imageMagickRotate($imgPath);
		return $status;
	}

	private function phpRotateJpeg($imgPath){
		$status = false;
		$source = imagecreatefromjpeg($imgPath);
		$rotate = imagerotate($source, $this->degree, 0);
		if($rotate){
			if(!imagejpeg($rotate,$imgPath, 93)) $status = false;
		}
		else $status = false;
		imagedestroy($source);
		imagedestroy($rotate);
		return $status;
	}

	private function jpegtranRotate($imgPath){
		exec('jpegtran -rotate '.$this->degree.' -trim '.$imgPath.' > '.$imgPath);
	}

	private function imageMagickRotate($imgPath){
		exec('convert "'.$imgPath.'" -rotate '.$this->degree.' "'.$imgPath.'" ;');
	}

	public function setTargetPath($path){
		$this->targetPath = $path;
	}

	public function setRecursive($bool){
		if($bool) $this->recursive = true;
		else $this->recursive = false;
	}

	public function setDegree($degree){
		$this->degree = $degree;
	}

	public function setRotateMode($mode){
		$this->rotateMode = $mode;
	}

	public function setMsgOutMode($mode){
		$this->msgOutMode = $mode;
	}

	private function msgOut($msgStr,$indent=0){
		if($this->msgOutMode == 1) echo str_repeat("\t",$indent).$msgStr."\n";
		elseif($this->msgOutMode == 2) echo '<li style="margin-left:'.($indent*10).'px">'.$msgStr.'</li>';
	}
}
?>