<?php

namespace framework\Http;

class File{
	
	private $name;
	private $type;
	private $size;
	private $tmp_name;
	private $errcode;
	private $errmsg;
	private $filename;
	private $path;
	
	public function __construct($file){
		$this->name = $file["name"];
		$this->type = $file["type"];
		$this->size = $file["size"]/1024; //kb
		$this->tmp_name = $file["tmp_name"];
		$this->errcode = $file["error"];
		switch($this->errcode) {
			case 1:
				$this->errmsg = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
				break;
			case 2:
				$this->errmsg = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
				break;
			case 3:
				$this->errmsg = '文件只有部分被上传';
				break;
			case 4:
				$this->errmsg = '没有文件被上传';
				break;
			case 6:
				$this->errmsg = '找不到临时文件夹';
				break;
			case 7:
				$this->errmsg = '文件写入失败';
				break;
		}
	}
	
    /**
     * get name of upload file
     *
     * @return string
     */
	public function getName(){
		return $this->name;
	}
	
    /**
     * get mime type of upload file
     *
     * @return string
     */
	public function getFileType(){
		return $this->type;
	}
	
    /**
     * get size of upload file
     *
     * @return int
     */
	public function getSize(){
		return $this->size;
	}

	/**
     * get size of upload file
     *
     * @return int
     */
	public function getFileSize(){
		return $this->size / 1024;
	}
	
    /**
     * get path upload file
     *
     * @return string
     */
	public function getFilePath(){
		return $this->tmp_name;
	}
	
    /**
     * get extension of upload file
     *
     * @return string
     */
	public function getFileExtension(){
		$pathinfo = pathinfo($this->getFilePath());
		return $pathinfo['extension'];
	}

	/**
     * get filename
     *
     * @return string
     */
	public function getFilename(){
		$this->filename = sprintf("%s.%s",uniqid(),$this->getFileExtension());
		return $this->filename;
	}
	
    /**
     * get error message of upload file
     *
     * @return string
     */
	public function getError(){
		return $this->errmsg;
	}
	
    /**
     * get error of upload file
     *
     * @return string
     */
	public function error(){	
		return ($this->errcode > 0) ? true : false ;
	}
}
?>