<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Admin\Common\Controller;
use App\Lib\Common\Captcha;
use App\Lib\Common\Encrypt;
use App\Lib\AdminService;
use App\Models\AdminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PublicController extends Controller
{
    /** @var AdminService */
    public $adminService;

    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->adminService = new AdminService();

    }

    public function login()
    {
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $params = $this->request->all();
                if (!(new Captcha())->check($params['captcha'])) {
                    throw new \Exception('验证码不正确');
                }
                $user = AdminModel::where(['username' => $params['username']])->first();
                if (!$user) {
                    throw new \Exception('用户名密码不正确');
                }
                if ($user['status'] == 0) {
                    throw new \Exception('您的账号已禁用，请联系管理员～');
                }
                if ($user['password'] != Encrypt::encryptPassword($params['password'], $user['salt'])) {
                    throw new \Exception('用户名密码不正确');
                }
                $user['last_login_time'] = time();
                $user->save();
                Session::put('user', $user);
                return $this->success('登录成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
        return $this->render('system/public/login');
    }

    public function logoutAction()
    {
//        \App::$session->set('user');
//        $this->redirect('system/public/login');
    }

    public function captcha()
    {
        (new Captcha())->createCheckCode()->showImage();
    }
}