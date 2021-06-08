<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\Common\Controller;
use App\Lib\System\MenuService;
use App\Models\MenuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Lumen\Routing\Router;

class MenuController extends Controller
{
    /** @var MenuService */
    public $menuService;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->menuService = new MenuService();
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
        $res = $this->menuService->getList($params);
        return $this->render('system/menu/index', [
            'params' => $params,
            'pagination' => $res->pageHtml(),
            'list' => $res->list,
        ]);

    }

    /**
     * @throws \Exception
     */
    public function editMenu()
    {
        $params = $this->request;
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $this->menuService->saveMenu($params->request->all());
                return $this->success('保存成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }

        }
        $model = null;
        $title = '创建菜单';
        if (isset($params['id']) && $params['id']) {
            $model = MenuModel::query()->where(['id' => $params['id']])->first();
            if (!$model) {
                throw new \Exception('菜单不存在');
            }
            $title = '编辑菜单-' . $model['id'];
        } else {
            $params['id'] = 0;
        }
        $childList = $this->menuService->getChildMenus();
        $methodList = $this->menuService->getAllMethodList($params['id']);
        return $this->render('system/menu/edit-menu', [
            'methodList' => $methodList,
            'childList' => $childList,
            'model' => $model,
            'title' => $title
        ]);
    }

    /**
     * @throws \Exception
     */
    public function setStatus()
    {
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $data = $this->request;
                MenuModel::query()->where(['id' => $data['id']])->update(['status' => $data['status']]);
                return $this->success('修改成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
    }
}