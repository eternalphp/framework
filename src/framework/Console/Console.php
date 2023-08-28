<?php

namespace framework\Console;

use framework\Container\Container;
use framework\Console\output\formatter\Style;
use framework\Exception\InvalidArgumentException;
use framework\Console\command;
use Exception;

class Console
{
	
	static $instance = null;
	private $processTitle = 'test';
	private $name = 'console';
	private $style;
	private $output;
	private $input;
	private $app;
	protected $defaultCommand  = 'list';
    protected $defaultCommands = [
        'help'             => command\Help::class,
        'list'             => command\Lists::class,
        'clear'            => command\Clear::class,
		'make'             => command\Make::class,
		'make:controller'  => command\Controller::class,
		'make:model'       => command\Model::class,
		'make:service'     => command\Service::class,
		'make:middleware'  => command\Middleware::class,
		'make:migration'   => command\Migration::class,
		'migration'        => command\MigrationRun::class,
        'serve'            => command\RunServer::class,
		'download'         => command\Download::class,
		'listen'           => command\Listen::class,
        'version'          => command\Version::class,
        'route:list'       => command\RouteList::class,
        'service:discover' => command\ServiceDiscover::class,
        'vendor:publish'   => command\VendorPublish::class,
		'view:clear'       => command\View::class,
    ];
	protected $commands = [];
	
	public function __construct(){
		
		$this->input = new Input();
		$this->output = new Output();
		$this->style = new Style();
		$this->app = Container::getInstance();
	}
	
	public static function getInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
    /**
     * 加载用户自定义指令
     */
	public function load(){
		if(file_exists(app_path('Console/Console.php'))){
			$commands = require(app_path('Console/Console.php'));
			if($commands){
				$this->defaultCommands = array_merge($this->defaultCommands,$commands);
			}
		}
	}
	
    /**
     * 配置指令
     */
    protected function configure()
    {
    }
	
    /**
     * 输出空行
     * @param int $count
     */
    public function newLine(int $count = 1)
    {
        $this->output->write(str_repeat(PHP_EOL, $count));
    }
	
    /**
     * 输出信息并换行
     * @param string $messages
     */
    public function writeln(string $messages)
    {
        $this->output->write($messages);
		$this->newLine();
    }
	
    /**
     * 输出信息
     * @param string $messages
     */
    public function write(string $messages)
    {
        $this->output->write($messages);
    }
	
    /**
     * 获取输出对象
     * return Output
     */
	public function getOutput(){
		return $this->output;
	}
	
    /**
     * 设置进程名称
     *
     * PHP 5.5+ or the proctitle PECL library is required
     *
     * @param string $title The process title
     *
     * @return $this
     */
    public function setProcessTitle($title)
    {
        $this->processTitle = $title;

        return $this;
    }
	
	public function loadCommands(){
        try {
            foreach($this->defaultCommands as $name=>$class){
                $this->getCommand($name);
            }
        }catch (InvalidArgumentException $e){
            $this->output->error($e->getMessage());
            $this->output->info("");
        }

	}
	
	public function getCommands(){
		return $this->commands;
	}
	
	public function getApp(){
		return $this->app;
	}
	
	public function getCommand($name){
		
		if(isset($this->commands[$name])){
			return $this->commands[$name];
		}
		
		if(isset($this->defaultCommands[$name])){
			$class = $this->defaultCommands[$name];

			if(class_exists($class)) {
                $this->app->bind($name, $class);
                $this->app->get($name)->configure();
                $this->commands[$name] = $this->app->get($name);

			    return $this->commands[$name];
            }else{
                throw new InvalidArgumentException("can not find class: $class");
            }

		}else{
			throw new InvalidArgumentException("can not find command: $name");
		}
	}
	
    /**
     * 获取命令行名称
     * return string
     */
	public function getName(){
		return $this->name;
	}
	
    /**
     * 获取默认命令行
     * return array
     */
	public function getDefaultCommands(){
		$strWidth = 20;
		$elements = array();
		$this->loadCommands();
		$commands = $this->getCommands();
		
		if($commands){
			
			$elements[] = "Available commands:";
			
			foreach($commands as $name=>$command){
				
				if($command->isGroup()){
					$elements[] = sprintf("  %s%s",str_pad("  " . $name,$strWidth," "),$command->getDescription());
				}else{
					$elements[] = sprintf("  %s%s",str_pad($name,$strWidth," "),$command->getDescription());
				}
			}
			
			$elements[] = "\n";
		}
		
		return $elements;
	}
	
	public function run(){
		
		$this->load();
		
		$name = $this->input->getCommandName();

		if($this->input->hasParameterOption(['-V','--version'])){
			$name = 'version';
		}
		
		if($this->input->hasParameterOption(['-h','--help'])){
			$name = 'help';
		}

		if($name == ''){
			$name = $this->defaultCommand;
		}
		
		if(isset($this->defaultCommands[$name])){
			$command = $this->getCommand($name);
			$this->input->bind($command);
			$command->execute($this->input,$this->output);
		}
	}
}
