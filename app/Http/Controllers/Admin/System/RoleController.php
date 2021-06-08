<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\Common\Controller;
use App\Lib\RoleService;
use App\Models\AdminModel;
use App\Models\MenuModel;
use App\Models\RoleAdminModel;
use App\Models\RoleMenuModel;
use App\Models\RoleModel;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /** @var RoleService */
    private $roleService;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->roleService = new RoleService();
    }

    /**
     * @throws \Exception
     */
    public function index()
    {
        $params = $this->request;
        $params['page'] = $this->request->get('page', 1);
        $params['page_size'] = $this->request->get('page_size', 10);
        if (!empty($params['search_type'])) {
            $params[$params['search_type']] = $params['search_value'];
        }
        $res = $this->roleService->getList($params);
        return $this->render('system/role/index', [
            'params' => $params,
            'pagination' => $res->pageHtml(),
            'list' => $res->list
        ]);
    }

    /**
     * @throws \Exception
     */
    public function editRole()
    {
        $params = $this->request;
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $this->roleService->saveRole($params->request->all());
                return $this->success('保存成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

        }
        $title = '创建角色';
        $model = null;
        if (!empty($params['id'])) {
            $model = RoleModel::query()->where(['id' => $params['id']])->first();
            if (!$model) {
                throw new \Exception('角色不存在');
            }
            $title = '编辑角色-' . $model['id'];
        }
        return $this->render('system/role/edit-role', [
            'model' => $model,
            'title' => $title
        ]);
    }


    /**
     * 设置用户角色
     * @throws \Exception
     */
    public function setRoleAdmin()
    {
        $params = $this->request;
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $this->roleService->setRoleAdmin($params->request->all());
                return $this->success('设置成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
        if (!isset($params['id']) || (int)$params['id'] == 0) {
            throw new \Exception('参数有误');
        }
        $model = RoleModel::query()->where(['id' => $params['id']])->first();
        $adminIdList = RoleAdminModel::query()->where(['role_id' => $params['id']])->pluck('admin_id')->toArray();
        $adminList = AdminModel::query()->where(['identity' => 0])->get()->toArray();
        return $this->render('system/role/set-role-admin', [
            'model' => $model,
            'adminList' => $adminList,
            'adminIdList' => $adminIdList
        ]);
    }


    /**
     * 设置角色权限
     * @throws \Exception
     */
    public function setRoleMenu()
    {
        $params = $this->request;
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $this->roleService->setRoleMenu($params->request->all());
                return $this->success('设置成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
        if (empty($params['id'])) {
            throw new \Exception('参数有误');
        }
        $model = RoleModel::query()->where(['id' => $params['id']])->first();
        $roleMenuIds = RoleMenuModel::query()->where(['role_id' => $params['id']])->pluck('menu_id')->toArray();
        $menuSelector = MenuModel::query()
            ->select(['id', 'parent_id as pId', 'name']);
        foreach (app()->config['auth.actionWhiteList'] as $route => $actions) {
            foreach ($actions as $action) {
                if ($action == '*') {
                    $menuSelector->where('url', 'not like', $route . '%');
                } else {
                    $menuSelector->where('url', '!=', $route . '/' . $action);
                }
            }
        }
        $menuList = $menuSelector->where(['status' => 1])
            ->orderBy('sort', 'desc')->orderBy('create_time', 'asc')->get()->toArray();
        foreach ($menuList as $key => $v) {
            if (!empty($roleMenuIds) && in_array($v['id'], $roleMenuIds)) {
                $menuList[$key]['checked'] = true;
            }
        }
        return $this->render('system/role/set-role-menu', [
            'model' => $model,
            'menuList' => $menuList
        ]);
    }
}