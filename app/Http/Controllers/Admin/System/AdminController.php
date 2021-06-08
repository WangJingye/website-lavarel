<?php

namespace App\Http\Controllers\Admin\System;

use App\Helper\SessionHelper;
use App\Helper\UploadHelper;
use App\Http\Controllers\Admin\Common\Controller;
use App\Lib\Common\Encrypt;
use App\Lib\System\AdminService;
use App\Models\AdminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /** @var AdminService */
    public $adminService;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->adminService = new AdminService();
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
        $res = $this->adminService->getList($params);
        return $this->render('system/admin/index', [
            'params' => $params,
            'pagination' => $res->pageHtml(),
            'list' => $res->list,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function editAdmin()
    {
        $params = $this->request;
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $params['avatar'] = UploadHelper::instance()->parseFileOrUrl('avatar', 'admin');
                $this->adminService->saveAdmin($params->request->all());
                return $this->success('保存成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
        $title = '创建账号';
        $model = null;
        if (!empty($params['id'])) {
            $model = AdminModel::query()->where(['id' => $params['id']])->first();
            if (!$model) {
                throw new \Exception('账号不存在');
            }
            $title = '编辑账号-' . $model['id'];
        }
        return $this->render('system/admin/edit-admin', [
            'model' => $model,
            'title' => $title
        ]);
    }

    /**　
     * 账号启用\禁用
     * @throws \Exception
     */
    public function setStatus()
    {
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $data = $this->request->request->all();
                AdminModel::query()->where(['id' => $data['id']])->update(['status' => $data['status']]);
                return $this->success('修改成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
    }

    public function profile()
    {
        return $this->render('system/admin/profile');
    }

    public function changePassword()
    {
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $params = $this->request->request->all();
                $user = Auth::user();
                if ($user['password'] != Encrypt::encryptPassword($params['password'], $user['salt'])) {
                    throw new \Exception('当前登录密码有误～');
                }
                if ($params['newPassword'] != $params['rePassword']) {
                    throw new \Exception('新密码与验证密码不一致～');
                }
                $this->adminService->changePassword($user, $params);
                SessionHelper::remove('user');
                return $this->success('修改成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
    }

    public function changeProfile()
    {
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $user = Auth::user();
                $params = $this->request->request->all();
                $params['avatar'] = UploadHelper::instance()->parseFileOrUrl('avatar', 'admin');
                $params['id'] = $user['id'];
                $params['username'] = $user['username'];
                $this->adminService->saveAdmin($params);
                foreach ($params as $k => $v) {
                    $user[$k] = $v;
                }
                SessionHelper::set('user', $user);
                return $this->success('修改成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
    }

    public function resetPassword()
    {
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $params = $this->request->request->all();
                $user = AdminModel::query()->where(['id' => $params['id']])->first();
                if (!$user) {
                    throw new \Exception('用户信息有误，请刷新重试');
                }
                $params['newPassword'] = app()->siteInfo['default_password'];
                $this->adminService->changePassword($user, $params);
                return $this->success('密码已重置');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
    }
}