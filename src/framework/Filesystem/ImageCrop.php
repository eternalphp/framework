<?php

namespace framework\Filesystem;

use framework\Filesystem\Image;

class ImageCrop
{
	
	private $image; //目标图片
	private $srcImage; //原图片
	private $width; //原图宽度
	private $height; //原图高度
	private $filename;
	private $mime;
	private $cropWidth; //选取宽度
	private $cropHeight; //选择高度
	private $cropX;
	private $cropY;
	private $thumbWidth; //缩略图宽度
	private $thumbHeight; //缩略图高度
	private $thumbX;
	private $thumbY;
	
	public function __construct($filename,$position = 'center'){
		$this->filename = $filename;
		$img = getimagesize($this->filename);
		$this->width = $img[0];
		$this->height = $img[1];
		$this->mime = $img['mime'];
		$this->image = new Image();
		
		$this->cropX = 0;
		$this->cropY = 0;
		$this->cropWidth = $this->width;
		$this->cropHeight = $this->height;
		
		switch($position){
			case 'left':
				if($this->cropWidth > $this->cropHeight){
					$this->cropWidth = $this->cropHeight;
				}elseif($this->cropHeight > $this->cropWidth){
					$this->cropHeight = $this->cropWidth;
				}
			break;
			case 'center':
				if($this->cropWidth > $this->cropHeight){
					$this->cropX = ($this->cropWidth - $this->cropHeight)/2;
					$this->cropWidth = $this->cropHeight;
				}elseif($this->cropHeight > $this->cropWidth){
					$this->cropY = ($this->cropHeight - $this->cropWidth)/2;
					$this->cropHeight = $this->cropWidth;
				}
			break;
			case 'right':
				if($this->cropWidth > $this->cropHeight){
					$this->cropWidth = $this->cropHeight;
					$this->cropX = ($this->cropWidth - $this->cropHeight);
				}elseif($this->cropHeight > $this->cropWidth){
					$this->cropHeight = $this->cropWidth;
					$this->cropY = ($this->cropHeight - $this->cropWidth);
				}
			break;
		}
		
		$this->thumbX = 0;
		$this->thumbY = 0;
		$this->thumbWidth = $this->cropWidth;
		$this->thumbHeight = $this->cropHeight;
	}
	
	/**
	 * set crop to image
	 *
	 * @param int $width
	 * @param int $height
	 * @param int $x
	 * @param int $y
	 * @return $this
	 */
	public function crop($filename,$width = 0,$height = 0,$x = 0,$y = 0){
		
		if($width > 0) $this->thumbWidth = $width;
		if($height > 0) $this->thumbHeight = $height;
		if($x > 0) $this->thumbX = $x;
		if($y > 0) $this->thumbY = $y;
		
		$this->image->truecolor()->create($this->thumbWidth,$this->thumbHeight);
		$this->srcImage = new Image();
		$this->srcImage->load($filename);
		
		imagecopyresampled($this->image->getImage(),$this->srcImage->getImage(), $this->thumbX, $this->thumbY, $this->cropX, $this->cropY, $this->thumbWidth, $this->thumbHeight, $this->cropWidth, $this->cropHeight);
		
		$type = $this->image->getFileExtension($this->filename);
		if($type == 'gif' || $type == 'png'){
			$this->image->transparent();
		}else{
			$this->image->interlace();
		}
		return $this;
	}
	
	/**
	 * save image
	 *
	 * @param string $filename
	 * @return void
	 */
	public function save($filename){
		$this->image->save($filename);
		$this->image->destroy($this->srcImage);
	}
	
	/**
	 * show image
	 *
	 * @return void
	 */
	public function show(){
		$this->image->save();
		$this->image->destroy($this->srcImage);
	}
}
?>