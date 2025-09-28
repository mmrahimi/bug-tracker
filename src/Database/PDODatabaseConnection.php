<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;
use App\Exceptions\BrokenConfigException;
use PDO;
use PDOException;

class PDODatabaseConnection implements DatabaseConnectionInterface
{
    protected $connection;

    protected $config;

    public const REQUIRED_CONFIGS = [
        'driver',
        'host',
        'database',
        'username',
        'password',
    ];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function connect()
    {
        if (!$this->isConfigValid($this->config)) {
            throw new BrokenConfigException();
        }

        $dsn = $this->generateDSN($this->config);

        try {
            $this->connection = new PDO($dsn);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }

        return $this;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function generateDSN($config)
    {
        return $config['driver'] . ":host=" . $config['host'] . ";dbname=" . $config['database'] . ";user=" . $config['username'] . ";password=" . $config['password'];
    }

    private function isConfigValid($config)
    {
        $matched = array_intersect(self::REQUIRED_CONFIGS, array_keys($config));

        return count($matched) === count(self::REQUIRED_CONFIGS);
    }
}
