<?php


namespace app\model;

use think\Model;

class BaseModel extends Model
{
    protected $autoWriteTimestamp = false;

    /**
     * 获取带分页的列表
     * @param int $limit
     * @param array $where
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getPageList(int $limit, array $where = [], string $field = "*", string $order = "id desc"): array
    {
        try {

            $list = $this->field($field)->where($where)->order($order)->paginate($limit);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $list);
    }

    /**
     * 获取所有的数据不分页
     * @param array $where
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getAllList(array $where = [], string $field = "*", string $order = "id desc"): array
    {
        try {

            $list = $this->field($field)->where($where)->order($order)->select();
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $list);
    }

    /**
     * 获取指定条目的数据
     * @param array $where
     * @param int $limit
     * @param string $field
     * @param string $order
     * @return array
     */
    public function getLimitList(array $where = [], int $limit = 10, string $field = "*", string $order = "id desc"): array
    {
        try {

            $list = $this->field($field)->where($where)->order($order)->limit($limit)->select();
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $list);
    }

    /**
     * 根据id获取信息
     * @param int $id
     * @param string $pk
     * @param string $field
     * @return array
     */
    public function getInfoById(int $id, string $pk = 'id', string $field = '*'): array
    {
        try {

            $info = $this->field($field)->where($pk, $id)->find();
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $info);
    }

    /**
     * 根据ids获取信息
     * @param $ids
     * @param string $pk
     * @param string $field
     * @return array
     */
    public function getInfoByIds($ids, string $pk = 'id', string $field = '*'): array
    {
        try {

            $list = $this->field($field)->whereIn($pk, $ids)->select();
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $list);
    }

    /**
     * 根据id获取信息
     * @param $where
     * @param string $field
     * @return array
     */
    public function findOne($where, $field = '*')
    {
        try {

            $info = $this->field($field)->where($where)->find();
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $info);
    }

    /**
     * 添加单条数据
     * @param $param
     * @return array
     */
    public function insertOne($param)
    {
        try {

            $id = $this->insertGetId($param);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '添加成功', $id);
    }
    /**
     * 添加单条数据 可自动维护时间戳
     * @param $param
     * @return array{code:int,msg:string,data:array<mixed>}
     */
    public function createOne($param)
    {
        try {

            $model = $this->create($param);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '添加成功', $model[$model->getPk()]);
    }

    /**
     * 批量添加
     * @param $param
     * @return array
     */
    public function insertBatch($param)
    {
        try {
            $this->insertAll($param);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '添加成功');
    }

    /**
     * 根据id更新数据
     * @param array $param
     * @param int $id
     * @param string $pk
     * @return array
     */
    public function updateById(array $param, int $id, string $pk = 'id'): array
    {
        try {

            $this->where($pk, $id)->update($param);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '更新成功');
    }

    /**
     * 根据where条件更新数据
     * @param array $param
     * @param array $where
     * @return array
     */
    public function updateByWhere(array $param, array $where): array
    {
        try {

            $this->where($where)->update($param);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '更新成功');
    }

    /**
     * 根据ids更新
     * @param array $param
     * @param $ids
     * @param string $pk
     * @return array
     */
    public function updateByIds(array $param, $ids, string $pk = 'id'): array
    {
        try {

            $this->whereIn($pk, $ids)->update($param);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '更新成功');
    }

    /**
     * 根据id删除
     * @param int $id
     * @param string $pk
     * @return array
     */
    public function delById(int $id, string $pk = 'id'): array
    {
        try {

            $this->where($pk, $id)->delete();
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '删除成功');
    }

    /**
     * 根据$where条件删除
     * @param array $where
     * @return array
     */
    public function delByWhere(array $where): array
    {
        try {

            $this->where($where)->delete();
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '删除成功');
    }

    /**
     * 根据ids删除
     * @param $ids
     * @param string $pk
     * @return array
     */
    public function delByIds($ids, string $pk = 'id'): array
    {
        try {

            $this->whereIn($pk, $ids)->delete();
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '删除成功');
    }

    /**
     * 检测数据唯一
     * @param array $where
     * @param int $id
     * @param string $pk
     * @return array
     */
    public function checkUnique(array $where, int $id = 0, string $pk = 'id'): array
    {
        try {

            if (empty($id)) {
                $has = $this->field($pk)->where($where)->find();
            } else {
                $has = $this->field($pk)->where($where)->where($pk, '<>', $id)->find();
            }
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, '获取成功', $has);
    }
}