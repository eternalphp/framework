<?php

namespace framework\Http;

use framework\Http\File;

class UploadedFile{
	
	private $files;
	private $allowExts = array('jpg','jpeg','gif','png','ppt','pdf','xls','xlsx','doc','docx','mp4','mp3');
	private $allowTypes = array();
	private $uploadMaxSize = 2*1024; //kb
	private $filename;
	private $savePath;
	private $error;

	
	public function __construct($path){
		$this->savePath = $path;
		$this->files = new File($_FILES);
		$this->filename = rtrim($this->savePath,'/') . $this->files->getFilename();
	}
	
    /**
     * set path to file
     *
	 * @param string $path
     * @return $this
     */
	public function savePath($path){
		if(!file_exists($path)){
			mkdir($path,0777,true);
		}
		$this->savePath = $path;
		return $this;
	}
	
    /**
     * set filename to file
     *
	 * @param string $filename
     * @return $this
     */
	public function fileName($filename){
		$this->filename = rtrim($this->savePath,'/') . $filename;
		return $this;
	}
	
    /**
     * set uploadMaxSize to file
     *
	 * @param int $size
     * @return $this
     */
	public function uploadSize($size){
		$this->uploadMaxSize = $size;
		return $this;
	}
	
    /**
     * set file extension to file
     *
	 * @param array $fileExts
     * @return $this
     */
	public function allowExts($fileExts = array()){
		$this->allowExts = $fileExts;
		return $this;
	}
	
    /**
     * set file mine type to file
     *
	 * @param array $allowTypes
     * @return $this
     */
	public function allowTypes($types = array()){
		$this->allowTypes = $types;
		return $this;
	}
	
    /**
     * save file to path
     *
	 * @param string $filename
     * @return bool
     */
	public function putFile($filename = null){
		
		if($filename != null){
			$this->filename = $filename;
		}
		
		if(file_exists($this->filename)){
			$this->error = '文件已存在！';
			return false;
		}
		
		$savePath = dirname($this->filename);
		if(!file_exists($savePath)){
			if(!mkdir($savePath,0777,true)){
				$this->error = '文件目录创建失败！';
				return false;
			}
		}
		
		if(!is_writable($savePath)){
			$this->error = '文件目录没有写入权限！';
			return false;
		}
		
		if(!move_uploaded_file($this->files->getFilePath(), $this->filename)) {
			$this->error = '文件上传保存错误！';
			return false;
		}
		
		return true;
	}
	
    /**
     * check upload file
     *
     * @return bool
     */
	public function isValid(){
		
		if(!$this->files->error()){
			$this->error = $this->files->getError();
			return false;
		}
		
		//文件类型
		if($this->allowExts && !in_array($this->files->getFileExtension(),$this->allowExts)){
			$this->error = '上传文件类型不允许';
			return false;
		}
		
		//文件后缀名
		if($this->allowTypes && !in_array($this->files->getFileType(),$this->allowTypes)){
			$this->error = '上传文件MIME类型不允许！'.$this->files->getFileType();
			return false;
		}
		
		//文件大小
		if($this->files->getFileSize() > $this->uploadMaxSize){
			$this->error = '上传文件大小不符！';
			return false;
		}
		
		if(!is_uploaded_file($this->files->getFilePath())){
			$this->error = '非法上传文件！';
			return false;
		}
		
		return true;
	}
	
    /**
     * get error of upload file
     *
     * @return string
     */
	public function getError(){
		return $this->error;
	}
}
?>