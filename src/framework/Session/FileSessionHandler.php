<?php

namespace framework\Session;

use SessionHandlerInterface;
use framework\Filesystem\Filesystem;

class FileSessionHandler implements SessionHandlerInterface
{
	
	private $path;
	private $minutes;
	private $files;
	
	public function __construct($path,$minutes = 120){
		$this->path = $path;
		$this->minutes = $minutes;
		$this->files = new Filesystem();
	}
	
    /**
     * open session
     *
     * @param string $save_path
	 * @param string $name
     * @return bool
     */
	public function open($save_path,$name){
		$this->path = $save_path;
		return true;
	}
	
    /**
     * read session
     *
     * @param string $sessionId
     * @return string
     */
	public function read($sessionId){
		if($this->files->exists($path = rtrim($this->path,'/').'/'.$sessionId)){
			if(time() - filemtime($path) <= $this->minutes*60){
				return $this->files->get($path);
			}
		}
		return '';
	}
	
    /**
     * write session
     *
     * @param string $sessionId
	 * @param string $data
     * @return bool
     */
	public function write($sessionId,$data){
		$this->files->put(rtrim($this->path,'/').'/'.$sessionId,$data,true);
		return true;
	}
	
    /**
     * destroy session
     *
     * @param string $sessionId
     * @return bool
     */
	public function destroy($sessionId){
		$this->files->delete(rtrim($this->path,'/').'/'.$sessionId);
		return true;
	}
	
    /**
     * gc session
     *
     * @param int $lifetime
     * @return bool
     */
	public function  gc($lifetime){
		$this->files->getFiles(rtrim($this->path,'/').'/',function($filename){
			if(time() - filemtime($filename) > $this->minutes*60){
				$this->files->delete($filename);
			}
		});
		
		return true;
	}
	
    /**
     * close session
     *
     * @return bool
     */
    public function close()
    {
        return true;
    }
}
?>