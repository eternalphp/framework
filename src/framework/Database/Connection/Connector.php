<?php

namespace framework\Database\Connection;

use framework\Database\Connection\MySqlConnector;
use framework\Database\Connection\MySqliConnector;


class Connector
{
    private $driver = null;

    public function __construct($config)
    {
        $driverName = $config["driver"] . "Connector";
        $namespace_class = __NAMESPACE__ . "\\" . $driverName;
        $this->driver = new $namespace_class($config);
    }

    /**
     * Create new connection
     *
     * @return $this
     */
    public function connect()
    {
        $this->driver->connect();
    }

    /**
     * close connection
     *
     * @return $this
     */
    public function close()
    {
        $this->driver->close();
    }

    /**
     * execute sql
     *
     * param string $sql
     * @return $this
     */
    public function query($sql)
    {
        $this->driver->query($sql);
        return $this;
    }

    /**
     * execute sql
     *
     * param string $sql
     * @return $this
     */
    public function execute($sql)
    {
        return $this->driver->execute($sql);
    }

    /**
     * get data lists
     *
     * @return $this
     */
    public function select()
    {
        return $this->driver->select();
    }

    /**
     * get data first row
     *
     * @return $this
     */
    public function find()
    {
        return $this->driver->find();
    }

    /**
     * get insert id
     *
     * @return int
     */
    public function insert()
    {
        return $this->driver->insert();
    }

    /**
     * Start transaction
     *
     * @return $this
     */
    public function startTrans()
    {
        $this->driver->startTrans();
    }

    /**
     * Transaction commit
     *
     * @return $this
     */
    public function commit()
    {
        $this->driver->commit();
    }

    /**
     * Transaction rollback
     *
     * @return $this
     */
    public function rollback()
    {
        $this->driver->rollback();
    }

    /**
     * Transaction end
     *
     * @return $this
     */
    public function transEnd()
    {
        $this->driver->transEnd();
    }

    /**
     * charset
     *
     * @return $this
     */
    public function charset($charset)
    {
        $this->driver->charset($charset);
    }

    /**
     * escape
     *
     * @param string $string
     * @return string
     */
    public function escape($string)
    {
        return $this->driver->escape($string);
    }
}

?>