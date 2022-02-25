<?php

namespace framework\Console;

use framework\Exception\RuntimeException;
use framework\Console\output\Formatter;

class PhpDevServe
{
	private $host = '127.0.0.1';
	
	private $port = 8080;
	
	private $bootFile = 'index.php';
	
	private $phpBin = 'php';
	
	private $docRoot = '';
	
	public function __construct($docRoot){
		$this->docRoot = $docRoot;
	}
	
	public function setPort($port){
		$this->port = $port;
		return $this;
	}
	
	public function setPhpBin($bin){
		$this->phpBin = $bin;
		return $this;
	}
	
	public function setDocRoot($root){
		$this->docRoot = $root;
		return $this;
	}
	
	public function getPhpBin(){
		return $this->phpBin;
	}
	
	public function getBootFile(){
		return $this->bootFile;
	}
	
    /**
     * start and listen serve
     *
     * @throws Exception
     */
    public function listen()
    {
		$this->printDefaultMessage();
        $command = $this->getCommand();
		exec($command,$result);
		print_r($result);
    }
	
    /**
     * build full command line string
     *
     * @param bool $checkEnv
     *
     * @return string
     * @throws Exception
     */
    public function getCommand()
    {
        $phpBin  = $this->getPhpBin();
        $svrAddr = $this->getServerAddr();
        // command eg: "php -S 127.0.0.1:8080 -t web web/index.php";
        $commands = array();
		$commands[] = "$phpBin -S $svrAddr";

        if ($docRoot = $this->docRoot) {
            if (!is_dir($docRoot)) {
                throw new RuntimeException("the document root is not exists. path: $docRoot");
            }

			$commands[] = "-t $docRoot";
        }

        return implode(" ",$commands);
    }
	
    /**
     * @throws Exception
     */
    protected function printDefaultMessage()
    {
        // $version = PHP_VERSION;
        $workDir = (string)getcwd();
        $svrAddr = $this->getServerAddr();
		
		if($this->docRoot == ''){
			$this->docRoot = $workDir;
		}
		
		$lines = array(
			"PHP Development Server start listening on <info>http://$svrAddr</info>",
			"Document root is <info>$this->docRoot</info>",
			"You can use <info>CTRL + C</info> to stop run."
		);
		
		foreach($lines as $line){
			$line = Formatter::getInstance()->format($line);
			Console::getInstance()->write($line);
		}
		
		Console::getInstance()->writeln('');
		
    }
	
    /**
     * @return int
     * @throws Exception
     */
    public function getRandomPort(): int
    {
        return random_int(10001, 59999);
    }
	
	public function getServerAddr(){
		return sprintf("%s:%s",$this->host,$this->port);
	}
}
