<?php

namespace framework\Container;

use ArrayAccess;

class Container extends AbstractContainer implements ArrayAccess {
	
	protected static $instance;
	
    /**
     * Set the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }
	
	public function offsetExists($offset)
	{
		return $this->has($offset);
	}
 
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}
 
	public function offsetSet($offset, $value)
	{
		return $this->bind($offset, $value);
	}
 
	public function offsetUnset($offset)
	{
		unset($this->instances[$offset]);
		unset($this->definitions[$offset]);
	}
}
?>