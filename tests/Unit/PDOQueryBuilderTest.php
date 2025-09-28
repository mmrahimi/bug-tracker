<?php

namespace Tests\Unit;

use App\Database\PDODatabaseConnection;
use App\Database\PDOQueryBuilder;
use App\Helpers\Config;
use PHPUnit\Framework\TestCase;

class PDOQueryBuilderTest extends TestCase
{
    protected PDOQueryBuilder $PDOQueryBuilder;

    public function setUp(): void
    {
        $config = Config::getConfig('database', 'pdo_testing');

        $PDOConnection = new PDODatabaseConnection($config);
        $this->PDOQueryBuilder = new PDOQueryBuilder($PDOConnection->connect());

        $this->PDOQueryBuilder->beginTransactions();

        parent::setUp();
    }

    public function testInsert()
    {
        $result = $this->insert();

        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    public function testUpdate()
    {
        $this->insert();

        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->where('user', 'johndoe')
            ->update(['email' => 'johndoe@example.com']);

        $this->assertequals(1, $result);
    }

    public function testDelete()
    {
        $this->insert();

        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->where('user', 'johndoe')
            ->delete();

        $this->assertequals(1, $result);
    }

    public function testIfMultipleWhereWorks()
    {
        $this->insert();
        $this->insert(['user' => 'MMR']);

        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->where('user', 'johndoe')
            ->where('link', 'http://www.test.com')
            ->update(['name' => 'After Multiple Where()']);

        $this->assertequals(1, $result);
    }

    public function testItCanFetchData()
    {
        $this->multipleInsertIntoDB(10);
        $this->multipleInsertIntoDB(10, ['user' => 'MMR']);

        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->where('user', 'MMR')
            ->get();

        $this->assertIsArray($result);
        $this->assertCount(10, $result);
    }

    public function testGetMethodCanFetchSpecificColumns()
    {
        $this->multipleInsertIntoDB(10);
        $this->multipleInsertIntoDB(10, ['user' => 'MMR']);

        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->where('user', 'MMR')
            ->get(['name', 'user']);

        $result = (array)$result[0];

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals(['name', 'user'], array_keys($result));
    }

    public function testItCanOnlyGetTheFirstRow()
    {
        $this->multipleInsertIntoDB(10, ['name' => 'first bug']);

        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->where('name', 'first bug')
            ->first();

        $this->assertIsObject($result);
        $this->assertObjectHasProperty('id', $result);
        $this->assertObjectHasProperty('name', $result);
        $this->assertObjectHasProperty('email', $result);
        $this->assertObjectHasProperty('link', $result);
        $this->assertObjectHasProperty('user', $result);
    }

    public function testItCanFindRowWithID()
    {
        $this->insert();
        $id = $this->insert(['user' => 'MMR']);

        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->find($id);

        $this->assertIsObject($result);
        $this->assertEquals('MMR', $result->user);
    }

    public function testItCanFindBy()
    {
        $this->insert();
        $id = $this->insert(['user' => 'MMR']);

        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->findBy('user', 'MMR');

        $this->assertIsObject($result);
        $this->assertEquals($result->id, $id);
    }

    public function testItReturnsEmptyArrayWhenRecordNotFound()
    {
        $this->multipleInsertIntoDB(3);
        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->where('user', 'MMR')
            ->get();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testItReturnsNullWhenFirstRecordNotFound()
    {
        $this->multipleInsertIntoDB(3);
        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->where('user', 'MMR')
            ->first();

        $this->assertNull($result);
    }

    public function testItReturnsZeroWhenUpdatingARecordThatDoesNotExist()
    {
        $this->multipleInsertIntoDB(3);
        $result = $this->PDOQueryBuilder
            ->table('bugs')
            ->where('user', 'MMR')
            ->update(['user' => 'UPDATE']);

        $this->assertEquals(0, $result);
    }

    private function insert($options = [])
    {
        $data = array_merge([
            'name' => 'bug',
            'link' => 'http://www.test.com',
            'user' => 'johndoe',
            'email' => 'john@doe.com',
        ], $options);

        return $this->PDOQueryBuilder->table('bugs')->create($data);
    }

    private function multipleInsertIntoDB($count = 1, $options = [])
    {
        for ($i = 0; $i < $count; $i++) {
            $this->insert($options);
        }
    }

    public function tearDown(): void
    {
        $this->PDOQueryBuilder->rollbackTransactions();

        parent::tearDown();
    }
}
