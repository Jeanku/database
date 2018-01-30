<?php

namespace Eloquent;

use Query\Builder as QueryBuilder;
use Query\Grammars\MySqlGrammar;

abstract class Model
{

    protected $db_name = 'cjjl_master';                     //todo改成配置

    /**
     * The table associated with the model.
     * @var string
     */
    protected $database;

    /**
     * The table associated with the model.
     * @var string
     */
    protected static $connection;



    /**
     * The table associated with the model.
     * @var string
     */
    protected static $grammar;


    /**
     * The table associated with the model.
     * @var string
     */
    protected $table;

    /**
     * The primary key for the model.
     * @var string
     */
    protected $primaryKey = 'id';


    /**
     * The model's attributes.
     * @var array
     */
    protected $attributes = [];

    /**
     * The model attribute's original state.
     * @var array
     */
    protected $original = [];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [];


    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [];

    /**
     * The array of booted models.
     * @var array
     */
    protected static $booted = [];

    /**
     * The array of global scopes on the model.
     * @var array
     */
    protected static $globalScopes = [];


    //表的创建时间字段
    const CREATED_AT = 'create_time';

    //表的更新时间字段
    const UPDATED_AT = 'update_time';

    //表的删除时间字段
    const DELETED_AT = null;

    //表的软删除字段无效值 1:有效 0:无效
    const INVALID_STATUS = 0;


    /**
     * construct function
     * @param  array $attributes
     */
    public function __construct(array $attributes = []) {}


    /**
     * Get the name of the "created at" column.
     * @return string
     */
    public function getCreatedAtColumn()
    {
        return static::CREATED_AT;
    }

    /**
     * Get the name of the "updated at" column.
     * @return string
     */
    public function getUpdatedAtColumn()
    {
        return static::UPDATED_AT;
    }

    /**
     * 获取当前时间
     * @return integer
     */
    public function freshTimestamp()
    {
        return time();
    }

    /**
     * 转换日期时间
     * @param  $mValue 日期时间
     * @return integer
     */
    public function fromDateTime($mValue)
    {
        return $mValue;
    }

    /**
     * Get a new query builder for the model's table.
     * @return \Eloquent\Builder
     */
    public function newQuery()
    {
        $builder = new QueryBuilder($this->getConnection(), $this->getGrammar());
        $builder->model($this)->from($this->getTable());
        return $builder;
    }

    /**
     * get a connection
     * @return array
     */
    public function getConnection()
    {
        if (isset(self::$connection[$this->db_name])) {
            return self::$connection[$this->db_name];
        } else {
            $swoolePdo = \Swoole::$php->db($this->db_name);
            self::$connection[$this->db_name] = new MySqliConnection($swoolePdo);
        }
        return self::$connection[$this->db_name];
    }


    /**
     * get a grammar
     * @return array
     */
    public function getGrammar()
    {
        if (self::$grammar) {
            return self::$grammar;
        } else {
            self::$grammar = new MySqlGrammar();
        }
        return self::$grammar ;
    }


    /**
     * Get the table associated with the model.
     * @return string
     */
    public function getTable()
    {
        if (isset($this->database)) {
            return $this->database . '.' .$this->table;
        }
        return $this->table;
    }

    /**
     * Get the value of the model's primary key.
     * @return mixed
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Get the queueable identity for the entity.
     * @return mixed
     */
    public function getQueueableId()
    {
        return $this->getKey();
    }

    /**
     * Get the primary key for the model.
     * @return string
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    /**
     * Set the primary key for the model.
     * @param  string $key
     * @return $this
     */
    public function setKeyName($key)
    {
        $this->primaryKey = $key;
        return $this;
    }

    /**
     * Get the table qualified key name.
     * @return string
     */
    public function getQualifiedKeyName()
    {
        return $this->getTable() . '.' . $this->getKeyName();
    }

    /**
     * Get the value of the model's route key.
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->getAttribute($this->getRouteKeyName());
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->getKeyName();
    }

    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps()
    {
        return $this->timestamps;
    }

    /**
     * Boot the soft deleting trait for a model.
     *
     * @author gaojian
     * @date   2017-10-26
     * @return void
     */
    public function getSoftDelete()                             //boot function
    {
        return isset($this->softDelete) ? $this->softDelete : false;
    }

    /**
     * Handle dynamic method calls into the model.
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $query = $this->newQuery();
        return call_user_func_array([$query, $method], $parameters);
    }

    /**
     * Handle dynamic static method calls into the method.
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new static;
        return call_user_func_array([$instance, $method], $parameters);
    }
}
