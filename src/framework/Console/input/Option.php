<?php

namespace framework\Console\input;

use framework\Exception\InvalidArgumentException;

class Option
{
	
    // 无需传值
    const VALUE_NONE     = 1;
    // 必须传值
    const VALUE_REQUIRED = 2;
    // 可选传值
    const VALUE_OPTIONAL = 4;
    // 传数组值
    const VALUE_IS_ARRAY = 8;
	
    /**
     * 选项名
     * @var string
     */
	private $name;
	
    /**
     * 选项短名称
     * @var string
     */
    private $shortcut;
	
    /**
     * 参数类型
     * @var int
     */
	private $type;
	
    /**
     * 参数默认值
     * @var mixed
     */
	private $default;
	
    /**
     * 参数描述
     * @var string
     */
    private $description;
	
    public function __construct(string $name, string $shortcut, int $type = 1){
		
		$this->name = $name;
		$this->type = $type;
		$this->description = '';
		$this->shortcut = $shortcut;
		$this->default = null;
		
	}
	
    /**
     * 获取选项名
     * @return string
     */
	public function getName(){
		return $this->name;
	}
	
    /**
     * 获取短名称
     * @return string
     */
    public function getShortcut()
    {
        return $this->shortcut;
    }
	
    /**
     * 设置短名称
     * @return string
     */
    public function setShortcut($shortcut)
    {
        $this->shortcut = $shortcut;
		return $this;
    }
	
    /**
     * 获取默认值
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }
	
    /**
     * 设置默认值
     * @return mixed
     */
    public function setDefault($default)
    {
		
        if (self::VALUE_NONE === (self::VALUE_NONE & $this->type) && null !== $default) {
            throw new InvalidArgumentException('Cannot set a default value when using InputOption::VALUE_NONE mode.');
        }

        if ($this->isArray()) {
            if (null === $default) {
                $default = [];
            } elseif (!is_array($default)) {
                throw new InvalidArgumentException('A default value for an array option must be an array.');
            }
        }
		
        $this->default = $this->acceptValue() ? $default : false;
		return $this;
    }

    /**
     * 获取描述
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
	
    /**
     * 设置描述
     * @return string
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
		return $this;
    }
	
    /**
     * 是否可以设置值
     * @return bool 类型不是 self::VALUE_NONE 的时候返回true,其他均返回false
     */
    public function acceptValue()
    {
        return $this->isValueRequired() || $this->isValueOptional();
    }

    /**
     * 是否必须
     * @return bool 类型是 self::VALUE_REQUIRED 的时候返回true,其他均返回false
     */
    public function isValueRequired()
    {
        return self::VALUE_REQUIRED === (self::VALUE_REQUIRED & $this->type);
    }

    /**
     * 是否可选
     * @return bool 类型是 self::VALUE_OPTIONAL 的时候返回true,其他均返回false
     */
    public function isValueOptional()
    {
        return self::VALUE_OPTIONAL === (self::VALUE_OPTIONAL & $this->type);
    }

    /**
     * 选项值是否接受数组
     * @return bool 类型是 self::VALUE_IS_ARRAY 的时候返回true,其他均返回false
     */
    public function isArray()
    {
        return self::VALUE_IS_ARRAY === (self::VALUE_IS_ARRAY & $this->type);
    }
	
    /**
     * 检查所给选项是否是当前这个
     * @param Option $option
     * @return bool
     */
    public function equals(Option $option)
    {
        return $option->getName() === $this->getName()
        && $option->getShortcut() === $this->getShortcut()
        && $option->getDefault() === $this->getDefault()
        && $option->isArray() === $this->isArray()
        && $option->isValueRequired() === $this->isValueRequired()
        && $option->isValueOptional() === $this->isValueOptional();
    }
}
