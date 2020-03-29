<?php

namespace framework\Session;

use SessionHandlerInterface;
use framework\Filesystem\Filesystem;

class NullSessionHandler implements SessionHandlerInterface
{
	
	public function __construct(){

	}
	
    /**
     * open session
     *
     * @param string $save_path
	 * @param string $name
     * @return bool
     */
	public function open($save_path,$name){
		return true;
	}
	
    /**
     * read session
     *
     * @param string $sessionId
     * @return string
     */
	public function read($sessionId){
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
		return true;
	}
	
    /**
     * destroy session
     *
     * @param string $sessionId
     * @return bool
     */
	public function destroy($sessionId){
		return true;
	}
	
    /**
     * gc session
     *
     * @param int $lifetime
     * @return bool
     */
	public function  gc($lifetime){
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