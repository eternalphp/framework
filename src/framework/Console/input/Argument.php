<?php

namespace framework\Console\input;

use framework\Exception\InvalidArgumentException;

class Argument
{
	
    // 必传参数
    const REQUIRED = 1;

    // 可选参数
    const OPTIONAL = 2;

    // 数组参数
    const IS_ARRAY = 4;
	
    /**
     * 参数名
     * @var string
     */
	private $name;
	
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
	
    public function __construct(string $name, int $type = 2){
		
		$this->name = $name;
		$this->type = $type;
		$this->description = '';
		$this->default = null;
		
	}
	
    /**
     * 获取参数名
     * @return string
     */
	public function getName(){
		return $this->name;
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
		
        if (self::REQUIRED === $this->type && null !== $default) {
            throw new InvalidArgumentException('Cannot set a default value except for InputArgument::OPTIONAL mode.');
        }

        if ($this->isArray()) {
            if (null === $default) {
                $default = [];
            } elseif (!is_array($default)) {
                throw new InvalidArgumentException('A default value for an array argument must be an array.');
            }
        }
		
        $this->default = $default;
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
     * 设置参数为必填类型
     * @return $this;
     */
	public function setRequired(){
		$this->type = self::REQUIRED;
		return $this;
	}
	
    /**
     * 设置参数为可选类型
     * @return $this;
     */
	public function setOptional(){
		$this->type = self::OPTIONAL;
		return $this;
	}
	
    /**
     * 设置参数为数组类型
     * @return $this;
     */
	public function setArray(){
		$this->type = self::IS_ARRAY;
		return $this;
	}
	
    /**
     * 是否必须
     * @return bool
     */
    public function isRequired()
    {
        return self::REQUIRED === (self::REQUIRED & $this->type);
    }

    /**
     * 该参数是否接受数组
     * @return bool
     */
    public function isArray()
    {
        return self::IS_ARRAY === (self::IS_ARRAY & $this->type);
    }
}
