<?php

$targetPath = $_REQUEST['path'];
$recursive = (isset($_REQUEST['recursive']) && !$_REQUEST['recursive']?false:true);
$degree = (isset($_REQUEST['degree'])?$_REQUEST['degree']:90);
$rotateMode = (isset($_REQUEST['rotmode'])?$_REQUEST['rotmode']:1);

if(!is_numeric($degree)) $degree = 90;
if(!is_numeric($rotateMode)) $rotateMode = 90;

$rotateManager = new ImageRotator();

$rotateManager->setRecursive($recursive);
$rotateManager->setDegree($degree);
$rotateManager->setRotateMode($rotateMode);

if($targetPath) $rotateManager->batchRotateImages($targetPath);
else echo 'ERROR: target path is empty';

class ImageRotator{
	private $targetPath = '';
	private $recursive = true;
	private $degree = 90;
	private $rotateMode = 1;		//1 = php, 2 = jpegtran, 3 = ImageMagick

	function __construct() {
	}

	function __destruct(){
	}

	public function batchRotateImages($targetPath){
		if($targetPath){
			if($fh = opendir($targetPath)){
				echo '<ul>';
				echo '<li>Stating directory: '.$targetPath.'</li>';
				while (false !== ($entry = readdir($fh))){
					if($entry != "." && $entry != ".."){
						if(is_file($targetPath.'/'.$entry)){
							echo '<li>Evaluating: '.$targetPath.'/'.$entry.'</li>';
							if(pathinfo($targetPath.'/'.$entry,PATHINFO_EXTENSION ) == 'jpg'){
								$imgInfoArr = getimagesize($targetPath.'/'.$entry);
								$ratio = $imgInfoArr[0]/$imgInfoArr[1];
								if($ratio < 1){
									echo '<li style="margin-left:15px">Rotating...</li>';
									$this->rotateImage($targetPath.'/'.$entry);
								}
								else echo '<li style="margin-left:15px">Skipping ('.$ratio.')</li>';
							}
							else echo '<li style="margin-left:15px">ERROR: not a jpg</li>';
						}
						elseif(is_dir($targetPath.'/'.$entry)){
							if($this->recursive) $this->batchRotateImages($targetPath.'/'.$entry);
						}
					}
				}
				echo '</ul>';
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
}


?>