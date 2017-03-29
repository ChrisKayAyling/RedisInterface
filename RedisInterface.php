<?php
namespace RedisInterface;

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

    public $errorInfo = array();


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
                    'database' => $Settings['Database']
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
     * @return array|bool
     */
    protected function queryAndSet($key, $query)
    {
        $rows = array();

        $results = $this->PDOLite->query($query);

        if ($results == FALSE) {
            $this->errorInfo = $this->PDOLite->errorInfo;
            return FALSE;
        } else {
            if (count($results) > 0) {
                foreach ($results as $row) {
                    $rows[] = $row;
                }

                if ($rows != NULL) {
                    $this->redis->set($key, serialize($rows));
                }

                return $rows;
            } else {
                return array();
            }
        }
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