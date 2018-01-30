<?php

namespace Eloquent;

use SqlLog;

class MySqliConnection
{
    /**
     * The active PDO connection.
     * @var PDO
     */
    protected $pdo;

    /**
     * Create a new database connection instance.
     * @param  \PDO|\Closure $pdo
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run a select statement against the database.
     * @param  string $query
     * @param  array $bindings
     * @return array
     */
    public function select($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings) {
            $statement = $this->getPdo()->query($this->getSql($query, $bindings));
            return $statement->fetchall();
        });
    }


    /**
     * Run an insert statement against the database.
     * @param  string $query
     * @param  array $bindings
     * @return bool
     */
    public function insert($query, $bindings = [])
    {
        return $this->statement($query, $bindings);
    }


    /**
     * Run an insert statement against the database.
     * @return bool
     */
    public function lastInsertId()
    {
        return $this->getPdo()->lastInsertId();
    }


    /**
     * Run an update statement against the database.
     * @param  string $query
     * @param  array $bindings
     * @return int
     */
    public function update($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Run a delete statement against the database.
     * @param  string $query
     * @param  array $bindings
     * @return int
     */
    public function delete($query, $bindings = [])
    {
        return $this->affectingStatement($query, $bindings);
    }

    /**
     * Execute an SQL statement and return the boolean result.
     *
     * @param  string $query
     * @param  array $bindings
     * @return bool
     */
    public function statement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings) {
            return $this->getPdo()->query($this->getSql($query, $bindings));
        });
    }

    /**
     * Run an SQL statement and get the number of rows affected.
     * @param  string $query
     * @param  array $bindings
     * @return int
     */
    public function affectingStatement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($query, $bindings) {
            $this->getPdo()->query($this->getSql($query, $bindings));
            return $this->getPdo()->getAffectedRows();
        });
    }


    /**
     * Execute a Closure within a transaction.
     * @param  \Closure $callback
     * @return mixed
     * @throws \Exception|\Throwable
     */
    public function transaction($callback)
    {
        $this->getPdo()->start();
        try {
            $result = $callback($this);
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $result;
    }


    /**
     * Commit the active database transaction.
     * @return void
     */
    public function commit()
    {
        $this->getPdo()->commit();
    }

    /**
     * Rollback the active database transaction.
     * @return void
     */
    public function rollBack()
    {
        $this->getPdo()->rollBack();
    }

    /**
     * get the pdo
     * @return void
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    /**
     * Run a SQL statement and log its execution context.
     * @param  string $query
     * @param  array $bindings
     * @param  \$callback
     * @return mixed
     * @throws \Exception
     */
    protected function run($query, $bindings, $callback = null)
    {
        try {
            $start = microtime(true);
            $result = $this->runQueryCallback($query, $bindings, $callback);
            $time = $this->getElapsedTime($start);
            $this->logQuery($query, $bindings, $time);
            return $result;
        } catch (\Exception $e) {
            SqlLog::log("【SQL】:" . $e->getMessage());
            throw new \Exception('sql not valid exception', -1);
        }
    }

    /**
     * Run a SQL statement.
     * @param  string $query
     * @param  array $bindings
     * @param  \Closure $callback
     * @return mixed
     * @throws \Exception
     */
    protected function runQueryCallback($query, $bindings, $callback)
    {
        return $callback($query, $bindings);
    }


    public function getSql($query, $bindings)
    {
        return vsprintf(str_replace('?', '\'%s\'', $query), $bindings);
    }
    /**
     * Log a query in the connection's query log.
     * @param  string $query
     * @param  array $bindings
     * @param  float|null $time
     * @return void
     */
    public function logQuery($query, $bindings, $time = null)
    {
        $query = $this->getSql($query, $bindings);
        SqlLog::log("【SQL】:$query; 【time】:{$time}");
    }


    /**
     * Get the elapsed time since a given starting point.
     * @param  int $start
     * @return float
     */
    protected function getElapsedTime($start)
    {
        return round((microtime(true) - $start) * 1000, 2);
    }

}
