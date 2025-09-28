<?php

namespace App\Database;

use App\Contracts\DatabaseConnectionInterface;

class PDOQueryBuilder
{
    protected $pdo;

    protected $table;

    protected $conditions;

    protected $values;

    protected $stmt;

    public function __construct(DatabaseConnectionInterface $connection)
    {
        $this->pdo = $connection->getConnection();
    }

    public function table(string $table)
    {
        $this->table = $table;

        return $this;
    }

    public function create(array $data)
    {
        $placeholders = [];

        foreach ($data as $key => $value) {
            $placeholders[] = '?';
        }

        $fields = implode(',', array_keys($data));
        $placeholders = implode(',', $placeholders);
        $this->values = array_values($data);
        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        $this->execute($sql);

        return (int)$this->pdo->lastInsertId();
    }

    public function where(string $field, string $value)
    {
        if (is_null($this->conditions)) {
            $this->conditions = "$field=?";
        } else {
            $this->conditions .= " AND $field=?";
        }

        $this->values[] = $value;

        return $this;
    }

    public function update(array $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key='$value'";
        }

        $fields = implode(',', $fields);
        $sql = "UPDATE {$this->table} SET $fields WHERE $this->conditions";
        $this->execute($sql);

        return $this->stmt->rowCount();
    }

    public function truncateAllTables()
    {
        $query = $this->pdo->prepare("SHOW TABLES");
        $query->execute();

        foreach ($query->fetchAll(\PDO::FETCH_COLUMN) as $table) {
            $this->pdo->prepare("TRUNCATE TABLE `$table`")->execute();
        }
    }

    public function delete()
    {
        $sql = "DELETE FROM {$this->table} WHERE $this->conditions";
        $this->execute($sql);

        return $this->stmt->rowCount();
    }

    public function get(array $fields = ['*'])
    {
        $fields = implode(',', $fields);
        $sql = "SELECT $fields FROM {$this->table} WHERE $this->conditions";
        $this->execute($sql);

        return $this->stmt->fetchAll();
    }

    public function first(array $fields = ['*'])
    {
        $data = $this->get($fields);

        return $data[0] ?? null;
    }

    public function find(int $id)
    {
        return $this->where('id', $id)->first();
    }

    public function findBy(string $field, mixed $value)
    {
        return $this->where($field, $value)->first();
    }

    public function execute(string $sql)
    {
        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute($this->values);
        $this->values = [];

        return $this;
    }

    public function beginTransactions()
    {
        $this->pdo->beginTransaction();
    }

    public function rollbackTransactions()
    {
        $this->pdo->rollBack();
    }
}
