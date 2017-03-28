<?php
namespace RedisInterface;

require_once 'vendor/autoload.php';

/**
 * RedisInterface
 *
 * RedisInterface is a Cache Query Interface
 *
 * @package ck\RedisInterface
 * @Author Chris Kay-Ayling
 */
class RedisInterface
{

    /**
     * @var \PDOLite\PDOLite
     */
    protected $PDOLite = NULL;

    /**
     * @var null|\Predis\Client
     */
    protected $redis = NULL;


    public function __construct($Settings)
    {

        try {
            $this->PDOLite = new \PDOLite\PDOLite(
                array(
                    'DatabaseHost' => $Settings['DatabaseHost'],
                    'DatabaseUser' => $Settings['DatabaseUser'],
                    'DatabasePass' => $Settings['DatabasePass'],
                    'DatabaseName' => $Settings['DatabaseName'],
                    'DatabasePort' => $Settings['DatabasePort'],
                    'DatabaseSocket' => $Settings['DatabaseSocket']
                )
            );
        } catch (\PDOException $e) {
            die("Please check datalayer settings.");
        }

        try {
            $this->redis = new \Predis\Client(
                array(
                    'scheme' => $Settings['Scheme'],
                    'host' => $Settings['Host'],
                    'port' => $Settings['Port'],
                    'path' => $Settings['Socket'],
                    'database' => $Settings['database']
                )
            );
        } catch (\RedisException $e) {
            die("Please check redis server settings.");
        }

    }

    /**
     * @param $query
     * @return string
     */
    public function quote($query)
    {
        return $this->PDOLite->quote($query);
    }


    /**
     * @param $query
     * @return bool|mixed
     */
    public function query($query)
    {

        $key = md5($_SERVER['HTTP_HOST'] . $query);

        if (FALSE == $this->redis->exists($key)) {
            return $this->queryAndSet($key, $query);
        } else {
            return unserialize($this->redis->get($key));
        }
    }

    /**
     * @param $key
     * @param $query
     * @return array
     */
    protected function queryAndSet($key, $query)
    {
        $rows = array();

        $results = $this->PDOLite->query($query);


        while ($row = $results) {
            $rows[] = $row;
        }

        if ($rows != NULL) {
            $this->redis->set($key, serialize($rows));
        }

        return $rows;

    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->redis->set($key, serialize($value));
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return unserialize($this->redis->get($key));
    }

    /**
     * @return bool
     */
    public function flush()
    {
        $this->redis->flushAll();
    }
}