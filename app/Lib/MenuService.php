<?php

namespace App\Lib;

use App\Helper\UrlHelper;
use App\Models\MenuModel;
use App\Models\RoleMenuModel;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Routing\Router;

class MenuService extends BaseService
{

    /**
     * @param $params
     * @param bool $ispage
     * @return \App\Helper\PageHelper|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|null
     */
    public function getList($params, $ispage = true)
    {
        $selector = MenuModel::query();
        if (isset($params['status']) && $params['status'] !== '') {
            $selector = $selector->where(['status' => $params['status']]);
        }
        if (isset($params['name']) && $params['name'] != '') {
            $selector = $selector->where('name like "%' . $params['name'] . '%"');
        }
        if (isset($params['url']) && $params['url'] != '') {
            $selector = $selector->where('url like "%' . $params['url'] . '%"');
        }
        if ($ispage) {
            return $this->pageHelper->pagination($selector, $params);
        }
        return $selector->get();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getTopList()
    {
        $rows = $this->getAdminMenus();
        $topList = [];
        foreach ($rows as $v) {
            if ($v['parent_id'] == 0) {
                $childList = $this->getChild($rows, $v['id']);
                foreach ($childList as $child) {
                    if ($child['url'] != '') {
                        $v['url'] = $child['url'];
                        $topList[] = $v;
                        break;
                    }
                }
            }
        }
        return $topList;
    }

    /**
     * @throws \Exception
     */
    public function getAdminMenus()
    {
        $selector = MenuModel::query()->where(['status' => 1]);
        if (Auth::user()->is_admin == 0) {
            $roleMenus = RoleMenuModel::query()->from('role_menu as a')
                ->leftJoin('role_admin as b', 'a.role_id', '=', 'b.role_id')
                ->where(['b.admin_id' => Auth::user()->id])
                ->pluck('a.menu_id')->toArray();
            $selector2 = MenuModel::query()->select(['id', 'parent_id']);
            foreach (app()->config['auth.actionWhiteList'] as $route => $actions) {
                foreach ($actions as $action) {
                    if ($action == '*') {
                        $selector2->orWhere(['url' => ['like', $route . '%']]);
                    } else {
                        $selector2->orWhere(['url' => $route . '/' . $action]);
                    }
                }
            }
            $lasts = $selector2->where(['status' => 1])->get()->toArray();
            $middles = [];
            $tops = [];
            if (count($lasts)) {
                $roleMenus = array_merge($roleMenus, array_column($lasts, 'id'));
                $lasts = array_column($lasts, 'parent_id');
                $roleMenus = array_merge($roleMenus, $lasts);
                $middles = MenuModel::query()->select(['id', 'parent_id'])->whereIn('id', $lasts)->get()->toArray();
            }
            if (count($middles)) {
                $tops = MenuModel::query()->select(['id'])->where(['id' => ['in', array_column($middles, 'parent_id')]])->pluck('id')->toArray();
                $roleMenus = array_merge($roleMenus, $lasts);
            }
            if (count($tops)) {
                $roleMenus = array_merge($roleMenus, $tops);
            }
            if (count($roleMenus)) {
                $selector->whereIn('id', $roleMenus);
            }
        }
        return $selector->orderBy('sort', 'desc')->orderBy('create_time')
            ->get()->toArray();
    }

    public function getChild($rows, $id)
    {
        $childList = [];
        foreach ($rows as $v) {
            if ($v['parent_id'] == $id) {
                $childList[] = $v;
                $arr = $this->getChild($rows, $v['id']);
                $childList = array_merge($childList, $arr);
            }
        }
        return $childList;
    }

    /**
     * 获取菜单列表
     * @param int $parent_id
     * @param int $i
     * @return array
     * @throws \Exception
     */
    public function getChildMenus()
    {
        return $this->getChildList(0, 0, 'menu', 'id', 'name', [], 'sort desc,create_time asc');
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getLeftList()
    {
        $menuList = $this->getAdminMenus();
        $activeList = $this->getActiveMenu();
        $topList = [];
        $leftList = [];
        foreach ($menuList as $v) {
            if ($v['parent_id'] == 0) {
                if (isset($activeList[$v['id']])) {
                    $top = $v;
                }
                $topList[] = $v;
            }
        }
        foreach ($menuList as $v) {
            if ($v['parent_id'] == $top['id']) {
                $leftList[$v['id']]['item'] = $v;
                $leftList[$v['id']]['list'] = [];
            }
        }
        foreach ($menuList as $v) {
            if (isset($leftList[$v['parent_id']])) {
                $leftList[$v['parent_id']]['list'][] = $v;
            }
        }
        return $leftList;
    }

    /**
     * @param $cmenu
     * @param $menuList
     * @return mixed
     * @throws \Exception
     */
    public function getParent($cmenu, $menuList)
    {
        $menuList[$cmenu['id']] = $cmenu;
        if ($cmenu['parent_id'] == 0) {
            return $menuList;
        }
        $menu = MenuModel::query()->where(['id' => $cmenu['parent_id']])->first();
        return $this->getParent($menu, $menuList);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getCurrentMenu()
    {
        $url = UrlHelper::instance()->getUri();
        if (empty($url) || $url == '/') {
            $url = 'erp/site-info/index';
        }
        return MenuModel::query()->where(['url' => $url])->first();
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function getActiveMenu()
    {
        $menu = $this->getCurrentMenu();
        $activeList = [];
        if ($menu) {
            $activeList = $this->getParent($menu, []);
        }
        return $activeList;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAllMethodList($menu_id = 0)
    {

        $arr[] = app()->config['auth.actionNoLoginList'];
        $arr[] = app()->config['auth.actionWhiteList'];
        $actionList = [];
        foreach ($arr as $ar) {
            foreach ($ar as $controller => $actions) {
                foreach ($actions as $action) {
                    if ($action == '*') {
                        $actionList[] = strtolower($controller);
                    }
                    $actionList[] = strtolower($controller . '/' . $action);
                }
            }
        }
        $actionList = array_values(array_unique($actionList));
        $existMethodList = MenuModel::query()->select(['url', 'id'])->where('url', '!=', '')->pluck('url', 'id')->toArray();
        $currentMethod = isset($existMethodList[$menu_id]) ? $existMethodList[$menu_id] : '';
        $routes = app('router')->getRoutes();
        $uriList = [];
        foreach ($routes as $route) {
            $uri = trim($route['uri'], '/');
            if ($uri==''){
                continue;
            }
            if (in_array($uri, $actionList)) {
                continue;
            }
            if ($currentMethod != $uri && in_array($uri, $existMethodList)) {
                continue;
            }
            if (in_array('web', $route['action']['middleware'] ?? [])) {
                $uriList[$uri] = $uri;
            }
        }
        return $uriList;
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public function saveMenu($data)
    {
        $selector = MenuModel::query();
        if (isset($data['id']) && $data['id']) {
            if ($data['id'] == $data['parent_id']) {
                throw new \Exception('不能选择自身作为父级功能');
            }
            $selector->where('id', '!=', $data['id']);
        }
        if (isset($data['id']) && $data['id']) {
            MenuModel::query()->where(['id' => $data['id']])->update($data);
        } else {
            $model = new MenuModel();
            $model->loadData($data);
            $model->save();
            $data['id'] = $model['id'];
        }
    }
}