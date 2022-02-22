<?php

namespace framework\Console\output\formatter;

use framework\Exception\InvalidArgumentException;

class Colors
{
	
    /**
     * The available colors
     *
     * @var array
     */
    protected $colors = [];
	
    /**
     * An array of default colors
     *
     * @var array $defaults
     */
    protected $defaults = [
            'default'       => 39,
            'black'         => 30,
            'red'           => 31,
            'green'         => 32,
            'yellow'        => 33,
            'blue'          => 34,
            'magenta'       => 35,
            'cyan'          => 36,
            'light_gray'    => 37,
            'dark_gray'     => 90,
            'light_red'     => 91,
            'light_green'   => 92,
            'light_yellow'  => 93,
            'light_blue'    => 94,
            'light_magenta' => 95,
            'light_cyan'    => 96,
            'white'         => 97,
        ];
	
	public function __construct(){
		foreach($this->defaults as $name => $code){
			$this->add(new Color($name,$code));
		}
	}
	
   /**
     * Add a color into the mix
     *
     * @param string  $key
     * @param integer $value
     */
    public function add(Color $color)
    {
        $this->colors[$color->getName()] = $color;
    }

    /**
     * Retrieve all of available colors
     *
     * @return array
     */
    public function all()
    {
        return $this->colors;
    }

    /**
     * Get the code for the color
     *
     * @param  string  $val
     *
     * @return string
     */
    public function get(string $name)
    {
        if (isset($this->colors[$name])) {
            return $this->colors[$name];
        }

        return $this->colors['default'];
    }
	
}
