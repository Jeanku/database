<?php

namespace Eloquent;

trait SoftDeletes
{

    protected $softDelete = true;

    /**
     * 获取删除列名
     * @author gaojian
     * @date   2017-10-26
     * @return string
     */
    public function getStatusColumn()
    {
        return defined('static::STATUS') ? static::STATUS : 'status';
    }

    /**
     * 获取删除列的完整名称
     * @author gaojian
     * @date   2017-10-26
     * @return string
     */
    public function getQualifiedStatusColumn()
    {
        return $this->getTable().'.'.$this->getStatusColumn();
    }

    /**
     * 获取表示无效的值
     * @author gaojian
     * @date   2017-10-26
     * @return int
     */
    public function getInvalidStatus()
    {
        return defined('static::INVALID_STATUS') ? static::INVALID_STATUS : 0;
    }

    /**
     * 获取删除时间列名
     * @author gaojian
     * @date   2015-04-27
     * @return string
     */
    public function getDeletedAtColumn()
    {
        return defined('static::DELETED_AT') ? static::DELETED_AT : 'deleted_at';
    }

    /**
     * 获取删除时间列的完整名称
     * @author gaojian
     * @date   2015-04-27
     * @return string
     */
    public function getQualifiedDeletedAtColumn()
    {
        return $this->getTable().'.'.$this->getDeletedAtColumn();
    }
}
