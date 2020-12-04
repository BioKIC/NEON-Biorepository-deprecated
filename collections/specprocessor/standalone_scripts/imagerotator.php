<?php


class ImageRotator{
	private $targetPath = '';
	private $recursive = true;
	private $degree = 90;

	function __construct() {
	}

	function __destruct(){
	}

	public function batchRotateImages($targetPath){
		if($targetPath){
			if($fh = opendir($targetPath)){
				while (false !== ($entry = readdir($fh))) {
					if($entry != "." && $entry != "..") {
						if(is_file($entry)){
							if(pathinfo($targetPath.'/'.$entry,PATHINFO_EXTENSION ) == 'jpg'){
								$imgInfoArr = getimagesize($targetPath.'/'.$entry);
								if($imgInfoArr[0]/$imgInfoArr[0] > 1){

								}
							}
						}
						elseif(is_dir($entry)){
							$this->batchRotateImages($targetPath.'/'.$entry);
						}
					}
				}
				closedir($fh);
			}
		}
	}

	private function phpRotateJpeg($imgPath){
		$source = imagecreatefromjpeg($imgPath);
		$rotate = imagerotate($source, $this->degree, 0);
		imagejpeg($rotate);
		imagedestroy($source);
		imagedestroy($rotate);
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
}


?>