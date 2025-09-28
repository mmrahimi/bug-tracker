<?php

namespace Tests\Unit;

use App\Exceptions\ConfigFileNotFoundException;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testIfGetFileContentsReturnsArray()
    {
        $fileContents = Config::getFileContents('database');

        $this->assertIsArray($fileContents);
    }

    public function testIfGetMethodReturnsValidData()
    {
        $config = Config::getConfig('database', 'pdo');

        $expected = [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'],
            'database' => $_ENV['DB_NAME'],
            'username' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
        ];

        $this->assertEquals($config, $expected);
    }

    public function testIfGetFileContentsThrowsAnExceptionWhenNoFileGetsFound()
    {
        $this->expectException(ConfigFileNotFoundException::class);
        Config::getFileContents('dummy');
    }
}
