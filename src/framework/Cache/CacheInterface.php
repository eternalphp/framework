<?php

namespace framework\Cache;


Interface CacheInterface
{
	
    /**
     * Returns an attribute.
     *
     * @param string $key    The attribute name
     * @param mixed  $default The default value if not found
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Sets an attribute.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value);
	
    /**
     * Removes an attribute.
     *
     * @param string $key
     *
     * @return mixed The removed value or null when it does not exist
     */
    public function remove($key);

    /**
     * Clears all attributes.
     */
    public function clear();
}
?>