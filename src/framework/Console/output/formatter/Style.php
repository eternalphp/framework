<?php

namespace framework\Console\output\formatter;

use framework\Exception\InvalidArgumentException;

class Style
{
	private $text;
    private $foreground;
	private $unForegroundCode = 39;
    private $background;
	private $unBackgroundCode = 49;
    private $options = [];
	private $colors;

    /**
     * 初始化输出的样式
     * @param string|null $foreground 字体颜色
     * @param string|null $background 背景色
     * @param array       $options    格式
     * @api
     */
    public function __construct()
    {
		$this->colors = new Colors();
    }

    /**
     * 设置字体颜色
     * @param string|null $color 颜色名
     * @throws \InvalidArgumentException
     * @api
     */
    public function setForeground(string $color)
    {
        $this->foreground = $this->colors->get($color);
		return $this;
    }

    /**
     * 设置背景色
     * @param string|null $color 颜色名
     * @throws \InvalidArgumentException
     * @api
     */
    public function setBackground(string $color)
    {
        $this->background = $this->colors->get($color);
		return $this;
    }

    /**
     * 设置字体格式
     * @param string $option 格式名
     * @throws \InvalidArgumentException When the option name isn't defined
     * @api
     */
    public function addOption(Option $option)
    {
		$this->options[$option->getName()] = $option;
		return $this;
    }
	
	public function bold(){
		$this->addOption(new Option('bold',1,22));
		return $this;
	}
	
	public function underscore(){
		$this->addOption(new Option('underscore',4,24));
		return $this;
	}
	
	public function blink(){
		$this->addOption(new Option('blink',5,25));
		return $this;
	}
	
	public function reverse(){
		$this->addOption(new Option('reverse',7,27));
		return $this;
	}
	
	public function conceal(){
		$this->addOption(new Option('conceal',8,28));
		return $this;
	}

    /**
     * 应用样式到文字
     * @param string $text 文字
     * @return string
     */
    public function text(string $text)
    {
		$this->text = $text;
		
        $setCodes   = [];
        $unsetCodes = [];

        if (null !== $this->foreground) {
            $setCodes[]   = $this->foreground->getCode();
            $unsetCodes[] = $this->unForegroundCode;
        }
        if (null !== $this->background) {
            $setCodes[]   = $this->background->getCode() + 10;
            $unsetCodes[] = $this->unBackgroundCode;
        }
        if ($this->options) {
            foreach ($this->options as $option) {
                $setCodes[]   = $option->getCode();
                $unsetCodes[] = $option->getUnsetCode();
            }
        }

        if (0 === count($setCodes)) {
            return $this->text;
        }

        return sprintf("\033[%sm%s\033[%sm", implode(';', $setCodes), $this->text, implode(';', $unsetCodes));
    }
}
