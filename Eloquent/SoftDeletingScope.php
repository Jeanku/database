<?php

namespace Eloquent;

class SoftDeletingScope
{


    /**
     * builder添加的拓展方法
     *
     * @var array
     */
    protected $extensions = ['ForceDelete', 'Restore', 'WithTrashed', 'OnlyTrashed', 'delete'];


    /**
     * 添加软删除过滤条件.
     *
     * @param  $builder
     * @param  $model
     * @return void
     */
    public function apply($builder, Model $model)
    {
        $builder->where($model->getQualifiedStatusColumn(), '!=', $this->getInvalidStatus($builder));
    }

    /**
     * 加载拓展方法.
     *
     * @param $builder
     * @return void
     */
    public function extend($builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * 获取删除列名
     *
     * @author gaojian
     * @date   2017-10-26
     * @param  $builder
     * @return string
     */
    protected function getStatusColumn($builder)
    {
//        if (count($builder->joins) > 0) {
//            return $builder->getModel()->getQualifiedStatusColumn();
//        } else {
            return $builder->getModel()->getStatusColumn();
//        }
    }

    /**
     * 获取删除时间列名
     *
     * @author gaojian
     * @date   2017-10-26
     * @param  $builder
     * @return string
     */
    protected function getDeletedAtColumn($builder)
    {
//        if (count($builder->getQuery()->joins) > 0) {
//            return $builder->getModel()->getQualifiedDeletedAtColumn();
//        } else {
            return $builder->getModel()->getDeletedAtColumn();
//        }
    }

    /**
     * 获取表示无效的值
     *
     * @author gaojian
     * @date   2017-10-26
     * @param  $builder
     * @return string
     */
    protected function getInvalidStatus($builder)
    {
        return $builder->getModel()->getInvalidStatus();
    }


    /**
     * 增加强制删除方法
     *
     * @author gaojian
     * @date   2017-10-26
     * @param  $builder
     * @return void
     */
    protected function addForceDelete($builder)
    {
        $builder->macro('forceDelete', function ($builder) {
            return $builder->delete();
        });
    }


    /**
     * 增加强制删除方法
     *
     * @author gaojian
     * @date   2017-10-26
     * @param  $builder
     * @return void
     */
    protected function addDelete($builder)
    {
        $builder->macro('delete', function ($builder) {                                                        //覆盖删除方法，达到软删除效果
            $column = $this->getStatusColumn($builder);
            $deletedAtColumn = $this->getDeletedAtColumn($builder);
            $data = array(
                $column => $this->getInvalidStatus($builder),
            );
            $deletedAtColumn && $data[$deletedAtColumn] = $builder->getModel()->freshTimestampString();         // 更新删除时间
            return $builder->update($data);
        });
    }

    /**
     * 增加恢复方法
     *
     * @author gaojian
     * @date   2017-10-26
     * @param  $builder
     * @return void
     */
    protected function addRestore($builder)
    {
        $builder->macro('restore', function ($builder, $defaultValue = 1) {
            $builder->withTrashed();
            return $builder->update(array($builder->getModel()->getStatusColumn() => $defaultValue));
        });
    }

    /**
     * 增加获取包括已软删除数据方法
     *
     * @author gaojian
     * @date   2017-10-26
     * @param  $builder
     * @return void
     */
    protected function addWithTrashed($builder)
    {
        $builder->macro('withTrashed', function ($builder) {
            return $builder;
        });
    }

    /**
     * 增加只获取软删除数据的方法
     *
     * @author gaojian
     * @date   2017-10-26
     * @param  $builder
     * @return void
     */
    protected function addOnlyTrashed($builder)
    {
        $builder->macro('onlyTrashed', function ($builder) {
            $model = $builder->getModel();
            $builder->where($model->getQualifiedStatusColumn(), $this->getInvalidStatus($builder));
            return $builder;
        });
    }

}
