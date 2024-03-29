<?php

namespace app\admin\service;

use app\admin\validate\ShippingTemplatesValidate;
use app\model\system\ShippingTemplates;
use app\model\system\ShippingTemplatesRegion;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\exception\ValidateException;
use think\facade\Db;

class ShippingService
{
    /**
     * 获取运费模板
     * @param array $param
     * @return array
     * @throws DbException
     */
    public function getList(array $param): array
    {
        $limit = $param['limit'];
        $name = $param['name'];

        $where[] = ['is_del', '=', 1];
        if (!empty($name)) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }

        $shippingTemplatesModel = new ShippingTemplates();
        $list = $shippingTemplatesModel->where($where)->order('sort desc')->paginate($limit);

        return dataReturn(0, 'success', $list);
    }

    /**
     * 添加运费模板
     * @param array $param
     * @return array
     */
    public function addShipping(array $param): array
    {
        // 检验完整性
        try {
            validate(ShippingTemplatesValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $shippingTemplatesModel = new ShippingTemplates();
        $has = $shippingTemplatesModel->checkUnique([
            'name' => $param['name']
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '运费模板名已经存在');
        }

        // 检测数据
        $ckRes = $this->checkParam($param);
        if ($ckRes['code'] != 0) {
            return $ckRes;
        }

        // 入库
        return $this->operateData($param, 1);
    }

    /**
     * 编辑运费模板
     * @param array $param
     * @return array
     */
    public function editShipping(array $param): array
    {
        // 检验完整性
        try {
            validate(ShippingTemplatesValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $shippingTemplatesModel = new ShippingTemplates();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['name', '=', $param['name']];
        $has = $shippingTemplatesModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '运费模板名已经存在');
        }

        // 检测数据
        $ckRes = $this->checkParam($param);
        if ($ckRes['code'] != 0) {
            return $ckRes;
        }

        // 入库
        return $this->operateData($param, 2);
    }

    /**
     * 模板信息
     * @param int $id
     * @return array
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function getShippingInfo(int $id): array
    {
        $shippingTemplatesModel = new ShippingTemplates();
        $shippingTemplatesRegionModel = new ShippingTemplatesRegion();
        $extend = $shippingTemplatesRegionModel->with(['province', 'city'])->where('tpl_id', $id)->select()->toArray();

        $country = [];
        foreach ($extend as $key => $vo) {
            if ($vo['province_id'] == 0 && $vo['city_id'] == 0) {
                $country = $vo;
                unset($extend[$key]);
                break;
            }
        }

        // 整理模板数据
        $extendMap = [];
        foreach ($extend as $vo) {
            $extendMap[$vo['uniqid']][] = $vo;
        }

        return dataReturn(0, 'success', [
            'info' => $shippingTemplatesModel->findOne([
                'id' => $id
            ])['data'],
            'extend' => $this->formatShowParam($extendMap),
            'country' => $country
        ]);
    }

    /**
     * 检测数据的合理性
     * @param array $param
     * @return array
     */
    private function checkParam(array $param): array
    {
        if ($param['type'] == 1) {
            $text = '首重';
            $text2 = '续件重';
        } else if ($param['type'] == 2) {
            $text = '首件';
            $text2 = '续件个数';
        } else {
            $text = '首体积';
            $text2 = '续件体积';
        }

        // 校验无效值
        if (count($param['first']) != count(array_filter($param['first']))) {
            return dataReturn(-1, $text . "数量必须大于0");
        }

        if (count($param['continue']) != count(array_filter($param['first']))) {
            return dataReturn(-2, $text2 . "数量必须大于0");
        }

        return dataReturn(0, '参数无误');
    }

    /**
     * 新增或者操作数据
     * @param array $param
     * @param int $flag
     * @return array
     */
    private function operateData(array $param, int $flag): array
    {
        $shippingTemplatesModel = new ShippingTemplates();
        $shippingTemplatesRegionModel = new ShippingTemplatesRegion();

        Db::startTrans();
        try {

            $tplParam = [
                'name' => $param['name'],
                'sort' => $param['sort'],
                'type' => $param['type'],
                'create_time' => now()
            ];

            if ($flag == 2) {
                unset($tplParam['create_time']);
                $tplParam['update_time'] = now();
                $res = $shippingTemplatesModel->updateById($tplParam, $param['id']);
                $shippingTemplatesRegionModel->delById($param['id'], 'tpl_id');
            } else {
                $res = $shippingTemplatesModel->insertOne($tplParam);
            }

            if ($res['code'] != 0) {
                Db::rollback();
                return dataReturn(-2, $res['msg']);
            }

            $regionMap = [];
            foreach ($param['area_id'] as $key => $vo) {

                $dataMap = explode('|', $vo);
                $groupId = uniqid();
                foreach ($dataMap as $v) {
                    $regionData = explode('-', $v);
                    $regionMap[] = [
                        'province_id' => $regionData[1],
                        'tpl_id' => ($flag == 1) ? $res['data'] : $param['id'],
                        'city_id' => $regionData[2],
                        'first' => $param['first'][$key],
                        'first_price' => $param['first_price'][$key],
                        'continue' => $param['continue'][$key],
                        'continue_price' => $param['continue_price'][$key],
                        'type' => $param['type'],
                        'uniqid' => $groupId
                    ];
                }
            }

            $res = (new ShippingTemplatesRegion())->insertBatch($regionMap);
            if ($res['code'] != 0) {
                Db::rollback();
                return dataReturn(-3, $res['msg']);
            }

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return dataReturn(-1, $e->getMessage());
        }

        if ($flag == 1) {
            $txt = '新增成功';
        } else {
            $txt = '编辑成功';
        }

        return dataReturn(0, $txt);
    }

    /**
     * 格式化显示数据
     * @param array $extendMap
     * @return array
     */
    public function formatShowParam(array $extendMap): array
    {
        $finalExtend = [];
        foreach ($extendMap as $vo) {

            $areaStr = '';
            $areaId = '';
            $provinceGroup = [];
            $cityGroup = [];
            $first = 0;
            $firstPrice = 0;
            $continue = 0;
            $continuePrice = 0;
            foreach ($vo as $v) {
                if ($v['province_id'] > 0 && $v['city_id'] == 0) {
                    $provinceGroup[] = $v;
                    $areaId .= '2-' . $v['province_id'] . '-0|';
                } else if ($v['province_id'] > 0 && $v['city_id'] > 0) {
                    $cityGroup[] = $v;
                    $areaId .= '3-' . $v['province_id'] . '-' . $v['city_id'] . '|';
                }

                $first = $v['first'];
                $firstPrice = $v['first_price'];
                $continue = $v['continue'];
                $continuePrice = $v['continue_price'];
            }

            foreach ($provinceGroup as $v) {
                $areaStr .= $v['province']['name'] . '(全省)、';
            }

            if (isset($cityGroup[0])) {
                $areaStr .= $cityGroup[0]['province']['name'] . '(';
            }

            foreach ($cityGroup as $v) {
                $areaStr .= $v['city']['name'] . '、';
            }

            $areaStr = rtrim($areaStr, '、');
            if (isset($cityGroup[0])) {
                $areaStr .= ')';
            }
            $areaId = rtrim($areaId, '|');

            $finalExtend[] = [
                'area_name' => $areaStr,
                'area_id' => $areaId,
                'first' => $first,
                'first_price' => $firstPrice,
                'continue' => $continue,
                'continue_price' => $continuePrice
            ];
        }

        return $finalExtend;
    }
}