<?php

namespace framework\Session;


Interface SessionInterface
{
	
    /**
     * Starts the session storage.
     *
     * @return bool True if session started
     *
     * @throws \RuntimeException If session fails to start.
     */
    public function start();
	
    /**
     * Returns the session ID.
     *
     * @return string The session ID
     */
    public function getId();

    /**
     * Sets the session ID.
     *
     * @param string $id
     */
    public function setId($id);
	
    /**
     * Returns an attribute.
     *
     * @param string $name    The attribute name
     * @param mixed  $default The default value if not found
     *
     * @return mixed
     */
    public function get($name, $default = null);

    /**
     * Sets an attribute.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value);
	
    /**
     * Removes an attribute.
     *
     * @param string $name
     *
     * @return mixed The removed value or null when it does not exist
     */
    public function remove($name);

    /**
     * Clears all attributes.
     */
    public function clear();
}
?>