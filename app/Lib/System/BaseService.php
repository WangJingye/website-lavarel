<?php

namespace App\Lib\System;

use App\Helper\PageHelper;
use Illuminate\Support\Facades\DB;

class BaseService
{
    public $pageHelper = null;

    public function __construct()
    {
        $this->pageHelper = new PageHelper();
    }

    /**
     * @param int $parent_id
     * @param int $i
     * @param $table
     * @param string $idField
     * @param string $nameField
     * @param array $additionWhere
     * @param string $order
     * @return array
     * @throws \Exception
     */
    public function getChildList($parent_id, $i, $table, $idField = 'id', $nameField = 'name', $additionWhere = [], $order = '')
    {
        $types = [];
        if ($parent_id == 0) {
            $types[] = ['id' => $parent_id, 'name' => '顶级目录'];
        }
        $selector = DB::table($table)->where(['parent_id' => $parent_id]);
        if (empty($additionWhere)) {
            $additionWhere = ['status' => 1];
        }
        $selector = $selector->where($additionWhere);
        if (empty($order)) {
            $order = $idField . ' asc';
        }
        $os = explode(',', $order);
        foreach ($os as $o) {
            $arr = explode(' ', $o);
            $selector = $selector->orderBy($arr[0], $arr[1]);
        }
        $rows = $selector->get();
        $rows = json_decode(json_encode($rows), true);
        $i++;
        foreach ($rows as $v) {
            $name = str_pad($v[$nameField], (strlen($v[$nameField]) + $i * 2), '--', STR_PAD_LEFT);
            $types[] = ['id' => $v[$idField], 'name' => $name];
            $childTypes = $this->getChildList($v[$idField], $i, $table, $idField, $nameField, $additionWhere);
            $types = array_merge($types, $childTypes);
        }
        return $types;
    }

    /**
     * 获取搜索下拉树状结构
     * @param $table
     * @param array $checked
     * @param string $idField
     * @param string $nameField
     * @param array $additionWhere
     * @param string $order
     * @return array
     * @throws \Exception
     */
    public function getTreeList($table, $checked = [], $idField = 'id', $nameField = 'name', $additionWhere = [], $order = '')
    {
        if (!is_array($checked)) {
            $checked = explode(',', $checked);
        }
        $needList = [];
        $depotList = DB::table($table)->where(['status' => 1])->get()->toArray();
        foreach ($depotList as $v) {
            $need = [];
            $need['id'] = $v[$idField];
            $need['pId'] = $v['parent_id'];
            $need['name'] = $v[$nameField];
            if (in_array($need['id'], $checked)) {
                $need['checked'] = true;
            }
            $needList[] = $need;
        }
        return $needList;
    }


    /**
     * 修复层级结构等级标志
     * @param $pid
     * @param $level
     * @param $table
     * @param $idField
     * @param $levelField
     * @throws \Exception
     */
    public function repairLevel($pid, $level, $table, $idField, $levelField)
    {
        $level++;
        $list = DB::table($table)->select([$idField])->where(['parent_id' => $pid])->get()->toArray();
        if (!count($list)) {
            return;
        }
        DB::table($table)->where(['parent_id' => $pid])->update([$levelField => $level]);
        foreach ($list as $v) {
            $this->repairLevel($v[$idField], $level, $table, $idField, $levelField);
        }
    }

}