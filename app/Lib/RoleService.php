<?php

namespace App\Lib;

use App\Helper\PageHelper;
use App\Models\RoleAdminModel;
use App\Models\RoleMenuModel;
use App\Models\RoleModel;
use Illuminate\Database\Query\Builder;

class RoleService extends BaseService
{
    /**
     * @param $params
     * @param bool $ispage
     * @return PageHelper|\Illuminate\Support\Collection|null
     */
    public function getList($params, $ispage = true)
    {
        $selector = new RoleModel();
        if (isset($params['status']) && $params['status'] != '') {
            $selector = $selector->where(['status' => $params['status']]);
        }
        if (isset($params['name']) && $params['name']) {
            $selector = $selector->where('name like "%' . $params['name'] . '%"');
        }
        if (isset($params['id']) && $params['id']) {
            $selector = $selector->where(['id' => $params['id']]);
        }
        if ($ispage) {
            return $this->pageHelper->pagination($selector, $params);
        }
        return $selector->get();
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public function saveRole($data)
    {
        /** @var Builder $selector */
        $selector = new RoleModel();
        if (isset($data['id']) && $data['id']) {
            $selector = $selector->where('id', '!=', $data['id']);
        }
        $row = $selector->where(['name' => $data['name']])->first();
        if ($row) {
            throw new \Exception('角色名称不能重复');
        }
        if (!empty($data['id'])) {
            RoleModel::query()->where(['id' => $data['id']])->update($data);
        } else {
            $model = new RoleModel();
            $model->loadData($data);
            $model->save();
        }
    }

    /**
     * @param $params
     * @throws \Exception
     */
    public function setRoleMenu($params)
    {
        $menuIds = explode(',', $params['menu_ids']);
        $roleMenus = RoleMenuModel::query()->where(['role_id' => $params['id']])->pluck('id', 'menu_id')->toArray();
        $addMenuIds = array_diff($menuIds, array_keys($roleMenus));
        $addMenuList = [];
        foreach ($addMenuIds as $key => $menuId) {
            $addMenuList[] = [
                'role_id' => $params['id'],
                'menu_id' => $menuId,
                'create_time' => time()
            ];
        }
        $removeAdminIds = array_diff(array_keys($roleMenus), $menuIds);
        if (count($removeAdminIds)) {
            RoleMenuModel::query()
                ->where(['role_id' => $params['id']])
                ->whereIn('menu_id', $removeAdminIds)->delete();
        }
        if (count($addMenuList)) {
            RoleMenuModel::query()->insert($addMenuList);
        }
    }

    /**
     * @param $params
     * @throws \Exception
     */
    public function setRoleAdmin($params)
    {
        $adminIdList = isset($params['admin_id']) ? $params['admin_id'] : [];
        $roleMenus = RoleAdminModel::query()->where(['role_id' => $params['id']])->pluck('id', 'admin_id')->toArray();
        $addAdminIds = array_diff($adminIdList, array_keys($roleMenus));
        $addAdminList = [];
        foreach ($addAdminIds as $key => $adminId) {
            $addAdminList[] = [
                'role_id' => $params['id'],
                'admin_id' => $adminId,
                'create_time' => time()
            ];
        }
        $removeAdminIds = array_diff(array_keys($roleMenus), $adminIdList);
        if (count($removeAdminIds)) {
            RoleAdminModel::query()
                ->where(['role_id' => $params['id']])
                ->whereIn('admin_id', $removeAdminIds)->delete();
        }
        if (count($addAdminList)) {
            RoleAdminModel::query()->insert($addAdminList);
        }
    }
}