<?php

namespace framework\Filesystem;


class Image
{
	
	private $image;
	private $srcImage;
	private $width;
	private $height;
	private $truecolor = false;
	
	public function __construct(){

	}
	
	public function create($width,$height){
		$this->width = $width;
		$this->height = $height;
		if($this->truecolor == true){
			$this->image = ImageCreateTrueColor($this->width,$this->height);
		}else{
			$this->image = ImageCreate($this->width,$this->height);
		}
		return $this;
	}
	
	public function load($filename){
		if(file_exists($filename)){
			$fileExt = $this->getFileExtension($filename);
			switch($fileExt){
				case 'jpg':
				case 'jpeg':
					$this->srcImage = imageCreateFromJPEG($filename);
				break;
				case 'png':
					$this->srcImage = imageCreateFromPNG($filename);
				break;
				case 'gif':
					$this->srcImage = imageCreateFromGIF($filename);
				break;
				default:
					$this->srcImage = imageCreateFromString(file_get_contents($filename));
			}
		}
		return $this->srcImage;
	}
	
	/**
	 * set color to image
	 *
	 * @param int $red
	 * @param int $green
	 * @param int $blue
	 * @return bool
	 */
	public function color($red,$green,$blue){
		return imageColorAllocate($this->image,$red,$green,$blue);
	}
	
	public function truecolor(){
		$this->truecolor = true;
		return $this;
	}
	
	public function transparent(){
		$backColor = $this->color(0, 255, 0);
		imagecolortransparent($this->image, $backColor); // 设置为透明色
		return $this;
	}
	
	public function interlace(){
		imageinterlace($this->image, 1);
	}
	
	public function crop(){
		$this->image->truecolor()->create($width,$height);
		imagecopyresampled($newImg->getImage(), $this->image->getImage(), $x, $y, $this->cropX, $this->cropY, $width, $height, $this->cropWidth, $this->cropHeight);
		$type = $this->image->getFileExtension($this->filename);
		if($type == 'gif' || $type == 'png'){
			$this->image->transparent();
		}else{
			$this->image->interlace();
		}
	}
	
	$this->image->thumb(function($cropImage){
		$cropImage->load($filename)
		->crop($width,$height,$x,$y)
		->save();
	})
	
	public function save($filename = null){
		if($filename != null){
			$fileExts = explode(".",basename($filename));
			$fileExt = strtolower($fileExt[1]);
			switch($fileExt){
				case 'jpg':
				case 'jpeg':
					imageJPEG($this->image,$filename);
				break;
				case 'png':
					imagePNG($this->image,$filename);
				break;
				case 'gif':
					imageGIF($this->image,$filename);
				break;
			}
		}else{
			imagePNG($this->image);
		}
	}
	
	public function destroy($image = null){
		imagedestroy($this->image);
		if($image != null){
			imagedestroy($image);
		}
	}
	
	public function getImage(){
		return $this->image;
	}
	
	public function getFileExtension($filename){
		$pathinfo = pathinfo($filename);
		return strtolower($pathinfo['extension']);
	}
}
?>