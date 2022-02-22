<?php

namespace framework\Console\input;

use framework\Console\Console;
use framework\Exception\InvalidArgumentException;

class Command
{
    /**
     * 指令名称
     * @var string
     */
	private $name;
	
	/**
     * @var Argument[]
     */
    private $arguments = [];
	
    private $requiredCount;
    private $hasAnArrayArgument = false;
    private $hasOptional;
	
    /**
     * @var Option[]
     */
    private $options = [];
	private $shortcuts = [];
	
    /**
     * 指令描述
     * @var string
     */
    private $description;
	
    /**
     * 帮助信息
     * @var string
     */
	private $help;
	
    /**
     * 用法介绍
     * @var string
     */
	private $usages = [];
	
	public function __construct(){

	}
	
    /**
     * 设置指令名
     * @return string
     */
    public function setName($name)
    {
        $this->name = $name;
		return $this;
    }
	
    /**
     * 获取指令名
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
	
    /**
     * 设置参数
     * @param Argument[] $arguments 参数数组
     */
    public function setArguments(array $arguments = [])
    {
        $this->arguments          = [];
        $this->requiredCount      = 0;
        $this->hasOptional        = false;
        $this->hasAnArrayArgument = false;
        $this->addArguments($arguments);
    }

    /**
     * 添加参数
     * @param Argument[] $arguments 参数数组
     * @api
     */
    public function addArguments(array $arguments = [])
    {
        if (null !== $arguments) {
            foreach ($arguments as $argument) {
                $this->addArgument($argument);
            }
        }
    }
	
    /**
     * 添加一个参数
     * @param Argument $argument 参数
     * @throws \LogicException
     */
    public function addArgument($name,callable $callback)
    {
		
		$argument = new Argument($name);
		call_user_func($callback,$argument);

        if (isset($this->arguments[$argument->getName()])) {
            throw new InvalidArgumentException(sprintf('[%s] An argument with name "%s" already exists.', $this->getName(),$argument->getName()));
        }

        if ($this->hasAnArrayArgument) {
            throw new InvalidArgumentException('Cannot add an argument after an array argument.');
        }

        if ($argument->isRequired() && $this->hasOptional) {
            throw new InvalidArgumentException('Cannot add a required argument after an optional one.');
        }

        if ($argument->isArray()) {
            $this->hasAnArrayArgument = true;
        }

        if ($argument->isRequired()) {
            ++$this->requiredCount;
        } else {
            $this->hasOptional = true;
        }

        $this->arguments[$argument->getName()] = $argument;
		
		return $this;
    }
	
    /**
     * 根据名称或者位置获取参数
     * @param string|int $name 参数名或者位置
     * @return Argument 参数
     * @throws \InvalidArgumentException
     */
    public function getArgument($name)
    {
        if (!$this->hasArgument($name)) {
            throw new InvalidArgumentException(sprintf('The "%s" argument does not exist.', $name));
        }

        $arguments = is_int($name) ? array_values($this->arguments) : $this->arguments;

        return $arguments[$name];
    }

    /**
     * 根据名称或位置检查是否具有某个参数
     * @param string|int $name 参数名或者位置
     * @return bool
     * @api
     */
    public function hasArgument($name)
    {
        $arguments = is_int($name) ? array_values($this->arguments) : $this->arguments;

        return isset($arguments[$name]);
    }
	
    /**
     * 获取参数数量
     * @return int
     */
    public function getArgumentCount()
    {
        return $this->hasAnArrayArgument ? PHP_INT_MAX : count($this->arguments);
    }

    /**
     * 获取必填的参数的数量
     * @return int
     */
    public function getArgumentRequiredCount()
    {
        return $this->requiredCount;
    }

    /**
     * 获取参数默认值
     * @return array
     */
    public function getArgumentDefaults()
    {
        $values = [];
        foreach ($this->arguments as $argument) {
            $values[$argument->getName()] = $argument->getDefault();
        }

        return $values;
    }
	
    /**
     * 获取所有参数
     * @return array
     */
	public function getArguments(){
		return $this->arguments;
	}
	
    /**
     * 设置选项
     * @param Option[] $options 选项数组
     */
    public function setOptions(array $options = [])
    {
        $this->options   = [];
        $this->shortcuts = [];
        $this->addOptions($options);
    }

    /**
     * 添加选项
     * @param Option[] $options 选项数组
     * @api
     */
    public function addOptions(array $options = [])
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }
	
    /**
     * 添加一个选项
     * @param Option $option 选项
     * @throws \LogicException
     * @api
     */
    public function addOption($name,$shortcut,callable $callback)
    {
		$option = new Option($name,$shortcut);
		call_user_func($callback,$option);
		
        if (isset($this->options[$option->getName()]) && !$option->equals($this->options[$option->getName()])) {
            throw new InvalidArgumentException(sprintf('An option named "%s" already exists.', $option->getName()));
        }

        if ($option->getShortcut()) {
            foreach (explode('|', $option->getShortcut()) as $shortcut) {
                if (isset($this->shortcuts[$shortcut])
                    && !$option->equals($this->options[$this->shortcuts[$shortcut]])
                ) {
                    throw new InvalidArgumentException(sprintf('An option with shortcut "%s" already exists.', $shortcut));
                }
            }
        }

        $this->options[$option->getName()] = $option;
        if ($option->getShortcut()) {
            foreach (explode('|', $option->getShortcut()) as $shortcut) {
                $this->shortcuts[$shortcut] = $option->getName();
            }
        }
    }
	
   /**
     * 根据名称获取选项
     * @param string $name 选项名
     * @return Option
     * @throws \InvalidArgumentException
     * @api
     */
    public function getOption(string $name): Option
    {
        if (!$this->hasOption($name)) {
            throw new InvalidArgumentException(sprintf('The "--%s" option does not exist.', $name));
        }

        return $this->options[$name];
    }

    /**
     * 根据名称检查是否有这个选项
     * @param string $name 选项名
     * @return bool
     * @api
     */
    public function hasOption(string $name)
    {
        return isset($this->options[$name]);
    }

    /**
     * 获取所有选项
     * @return Option[]
     * @api
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 根据名称检查某个选项是否有短名称
     * @param string $name 短名称
     * @return bool
     */
    public function hasShortcut(string $name)
    {
        return isset($this->shortcuts[$name]);
    }

    /**
     * 根据短名称获取选项
     * @param string $shortcut 短名称
     * @return Option
     */
    public function getOptionForShortcut(string $shortcut)
    {
        return $this->getOption($this->shortcutToName($shortcut));
    }

    /**
     * 获取所有选项的默认值
     * @return array
     */
    public function getOptionDefaults()
    {
        $values = [];
        foreach ($this->options as $option) {
            $values[$option->getName()] = $option->getDefault();
        }

        return $values;
    }

    /**
     * 根据短名称获取选项名
     * @param string $shortcut 短名称
     * @return string
     * @throws \InvalidArgumentException
     */
    private function shortcutToName(string $shortcut)
    {
        if (!isset($this->shortcuts[$shortcut])) {
            throw new InvalidArgumentException(sprintf('The "-%s" option does not exist.', $shortcut));
        }

        return $this->shortcuts[$shortcut];
    }
	
    /**
     * 获取该指令的介绍
     * @param bool $short 是否简洁介绍
     * @return string
     */
    public function getSynopsis(bool $short = false)
    {
        $elements = [];

        if ($short && $this->getOptions()) {
            $elements[] = '[options]';
        } elseif (!$short) {
            foreach ($this->getOptions() as $option) {
                $value = '';
                if ($option->acceptValue()) {
                    $value = sprintf(' %s%s%s', $option->isValueOptional() ? '[' : '', strtoupper($option->getName()), $option->isValueOptional() ? ']' : '');
                }

                $shortcut   = $option->getShortcut() ? sprintf('-%s|', $option->getShortcut()) : '';
                $elements[] = sprintf('[%s--%s%s]', $shortcut, $option->getName(), $value);
            }
        }

        if (count($elements) && $this->getArguments()) {
            $elements[] = '[--]';
        }

        foreach ($this->getArguments() as $argument) {
            $element = '<' . $argument->getName() . '>';
            if (!$argument->isRequired()) {
                $element = '[' . $element . ']';
            } elseif ($argument->isArray()) {
                $element .= ' (' . $element . ')';
            }

            if ($argument->isArray()) {
                $element .= '...';
            }

            $elements[] = $element;
        }

        return implode(' ', $elements);
    }
	
    /**
     * 获取描述文字
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
	
    /**
     * 设置描述文字
     * @return string
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
		return $this;
    }
	
    /**
     * 设置帮助信息
     * @param string $help
     * @return Command
     */
    public function setHelp(string $help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * 获取帮助信息
     * @return string
     */
    public function getHelp()
    {
        return $this->help ?: '';
    }
	
    /**
     * 描述信息
     * @return string
     */
    public function getProcessedHelp()
    {
        $name = $this->name;

        $placeholders = [
            '%command.name%',
            '%command.full_name%',
        ];
        $replacements = [
            $name,
            $_SERVER['PHP_SELF'] . ' ' . $name,
        ];

        return str_replace($placeholders, $replacements, $this->getHelp());
    }
	
    /**
     * 添加用法介绍
     * @param string $usage
     * @return $this
     */
    public function addUsage(string $usage)
    {
        if (0 !== strpos($usage, $this->name)) {
            $usage = sprintf('%s %s', $this->name, $usage);
        }

        $this->usages[] = $usage;

        return $this;
    }

    /**
     * 获取用法介绍
     * @return array
     */
    public function getUsages()
    {
        return $this->usages;
    }
	
	public function getConsole(){
		return Console::getInstance();
	}
	
	public function describe(){
		$elements = [];
		$elements[] = sprintf("version %s",application()->version());
		$elements[] = "\n";
		
		$strWidth = 20;
		
		$usages = $this->getUsages();
		if($usages){
			$elements[] = "Usage:";
			foreach($usages as $usage){
				$elements[] = sprintf("  %s",$usage);
			}
			$elements[] = "\n";
		}
		
		$arguments = $this->getArguments();
		if($arguments){
			$elements[] = "Arguments:";

			foreach($arguments as $name => $argument){
				$name = str_pad($name,$strWidth," ");
				$elements[] = sprintf("  %s%s",$name,$argument->getDescription());
			}
			$elements[] = "\n";
		}
		
		$options = $this->getOptions();
		if($options){
			
			$elements[] = "Options:";

			foreach($options as $name => $option){
				$name = str_pad(sprintf("-%s, --%s",$option->getShortcut(),$name),$strWidth," ");
				$elements[] = sprintf("  %s%s",$name,$option->getDescription());
			}
			$elements[] = "\n";
		}

		return $elements;
	}
	
	public function getStrWidth($options = array()){
		$total = 0;
		foreach($options as $option){
			if(strlen($option) > $total){
				$total = strlen($option);
			}
		}
		return $total;
	}
}
