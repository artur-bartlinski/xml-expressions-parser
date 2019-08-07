<?php

namespace App\Core\Database;

use PDO;

class QueryBuilder
{
    /**
     * The PDO instance.
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * Create a new QueryBuilder instance.
     *
     * @param PDO $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Select all records from a database table.
     *
     * @param string $table
     * @return array
     */
    public function selectAll($table)
    {
        $statement = $this->pdo->prepare("select * from {$table}");

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    /**
     * Select one record from a database table.
     *
     * @param string $table
     * @param $key
     * @param $value
     * @return array
     */
    public function selectOne($table, $key, $value)
    {
        $statement = $this->pdo->prepare("select * from {$table} where {$key}={$value}");

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_CLASS);
    }

    /**
     * Insert a record into a table.
     *
     * @param  string $table
     * @param  array  $parameters
     */
    public function insert($table, $parameters)
    {
        $sql = sprintf(
            'insert into %s (%s) values (%s)',
            $table,
            implode(', ', array_keys($parameters)),
            ':' . implode(', :', array_keys($parameters))
        );

        try {
            $statement = $this->pdo->prepare($sql);

            $statement->execute($parameters);
        } catch (\Exception $e) {
            //
        }
    }

    /**
     * Update a record in a table.
     *
     * @param string $table
     * @param $key
     * @param $value
     * @param array $parameters
     */
    public function update($table, $key, $value, $parameters)
    {
        $updateString = '';

        foreach ($parameters as $key => $value) {
            $updateString .= $key . ' = :' . $key . ', ';
        }

        $sql = sprintf(
            'update %s set %s where %s = %s',
            $table,
            $updateString,
            $key,
            $value
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
        } catch (\Exception $e) {
            //
        }
    }

    /**
     * Delete record from a database table.
     *
     * @param string $table
     * @param $key
     * @param $value
     * @return void
     */
    public function delete($table, $key, $value)
    {
        try {
            $statement = $this->pdo->prepare("delete from {$table} where {$key} = {$value}");
            $statement->execute();
        } catch (\Exception $e) {
            //
        }
    }
}
