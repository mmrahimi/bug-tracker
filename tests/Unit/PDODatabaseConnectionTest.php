<?php

namespace Tests\Unit;

use App\Database\PDODatabaseConnection;
use App\Exceptions\BrokenConfigException;
use App\Helpers\Config;
use PDO;
use PHPUnit\Framework\TestCase;

class PDODatabaseConnectionTest extends TestCase
{
    public function testGetConnectionReturnsPDOInstance()
    {
        $config = Config::getConfig('database', 'pdo_testing');

        $PDOConnection = new PDODatabaseConnection($config);
        $connection = $PDOConnection->connect()->getConnection();

        $this->assertInstanceOf(PDO::class, $connection);
    }

    public function testThatInvalidConfigLeadsToException()
    {
        $this->expectException(\PDOException::class);

        $config = Config::getConfig('database', 'pdo_testing');
        $config['database'] = 'dummy';

        $PDOConnection = new PDODatabaseConnection($config);
        $PDOConnection->connect();
    }

    public function testThatBrokenConfigLeadsToException()
    {
        $this->expectException(BrokenConfigException::class);

        $config = Config::getConfig('database', 'pdo_testing');
        unset($config['database']);

        $PDOConnection = new PDODatabaseConnection($config);
        $PDOConnection->connect();
    }
}
