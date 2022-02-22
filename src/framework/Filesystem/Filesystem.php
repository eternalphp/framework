<?php

namespace framework\Filesystem;

use Exception;

class Filesystem
{
	
	public function __construct(){

	}
	
    /**
     * Determine if a file or directory exists.
     *
     * @param  string  $path
     * @return bool
     */
	public function exists($path){
		return file_exists($path);
	}
	
    /**
     * Write the contents of a file.
     *
     * @param  string  $path
     * @param  string  $contents
     * @param  bool  $lock
     * @return int
     */
    public function put($path, $content, $lock = false)
    {
		if(!$this->exists(dirname($path))){
			mkdir(dirname($path),0777,true);
		}
        return file_put_contents($path, $content, $lock ? LOCK_EX : 0);
    }
	
    /**
     * Append to a file.
     *
     * @param  string  $path
     * @param  string  $content
     * @return int
     */
    public function append($path, $content)
    {
        return file_put_contents($path, $content, FILE_APPEND);
    }
	
    /**
     * Prepend to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public function prepend($path, $content)
    {
        if ($this->exists($path)) {
            return $this->put($path, $content.$this->get($path));
        }

        return $this->put($path, $data);
    }
	
    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @param  bool  $lock
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get($path, $lock = false)
    {
        if ($this->isFile($path)) {
            return $lock ? $this->sharedGet($path) : file_get_contents($path);
        }

        throw new Exception("File does not exist at path {$path}");
    }
	
    /**
     * Move a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public function move($path, $target)
    {
        return rename($path, $target);
    }

    /**
     * Copy a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public function copy($path, $target)
    {
        return copy($path, $target);
    }

    /**
     * Extract the file name from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function name($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Extract the trailing name component from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function basename($path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Extract the parent directory from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function dirname($path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Extract the file extension from a file path.
     *
     * @param  string  $path
     * @return string
     */
    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get the file type of a given file.
     *
     * @param  string  $path
     * @return string
     */
    public function type($path)
    {
        return filetype($path);
    }

    /**
     * Get the mime-type of a given file.
     *
     * @param  string  $path
     * @return string|false
     */
    public function mimeType($path)
    {
        return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);
    }

    /**
     * Get the file size of a given file.
     *
     * @param  string  $path
     * @return int
     */
    public function size($path)
    {
        return filesize($path);
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public function lastModified($path)
    {
        return filemtime($path);
    }

    /**
     * Determine if the given path is a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public function isDirectory($directory)
    {
        return is_dir($directory);
    }

    /**
     * Determine if the given path is writable.
     *
     * @param  string  $path
     * @return bool
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * Determine if the given path is a file.
     *
     * @param  string  $file
     * @return bool
     */
    public function isFile($file)
    {
        return is_file($file);
    }
	
    /**
     * Get contents of a file with shared access.
     *
     * @param  string  $path
     * @return string
     */
    public function sharedGet($path)
    {
        $contents = '';

        $handle = fopen($path, 'rb');

        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);

                    $contents = fread($handle, $this->size($path) ?: 1);

                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }

        return $contents;
    }
	
    /**
     * Delete the file at a given path.
     *
     * @param  string  $path
     * @return bool
     */
    public function delete($path)
    {
		if(file_exists($path)){
			if($this->isFile($path)){
				return unlink($path);
			}else{
				$this->getFiles($path,function($filename){
					$this->delete($filename);
				});
				rmdir($path);
			}
		}
    }
	
    /**
     * Get files to callback
     *
     * @param  string  $path
	 * @param callable $callback
     * @return void
     */
    public function getFiles($path,$callback)
    {
		$path = rtrim($path,'/').'/';
		if(file_exists($path)){
			$handle = dir($path);
			while(($file = $handle->read()) !== false){
				if($file != '.' && $file != '..'){
					$filename = $path . $file;
					if($this->isFile($filename)){
						call_user_func($callback,$filename,'file');
					}else{
						$this->getFiles($filename,$callback);
						call_user_func($callback,$filename,'dir');
					}
				}
			}
		}
    }
}
?>