<?php

namespace App\Lib\System;


use App\Lib\Common\Encrypt;
use App\Models\AdminModel;

class AdminService extends BaseService
{
    /**
     * @param $params
     * @param bool $ispage
     * @return \App\Helper\PageHelper|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|null
     */
    public function getList($params, $ispage = true)
    {
        $selector = AdminModel::query();
        if (isset($params['status']) && $params['status'] !== '') {
            $selector = $selector->where(['status' => $params['status']]);
        }
        if (isset($params['id']) && $params['id'] !== '') {
            $selector = $selector->where(['id' => $params['id']]);
        }
        if (isset($params['username']) && $params['username'] != '') {
            $selector = $selector->where('username', 'like', '"%' . $params['username'] . '%"');
        }
        if (isset($params['realname']) && $params['realname'] != '') {
            $selector = $selector->where('realname', 'like', '"%' . $params['realname'] . '%"');
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
    public function saveAdmin($data)
    {
        $selector = AdminModel::query();
        if (!empty($data['id'])) {
            $selector->where('id', '!=', $data['id']);
        }
        $row = $selector->where(['username' => $data['username']])->first();
        if ($row) {
            throw new \Exception('用户名不能重复');
        }

        if (!empty($data['id'])) {
            AdminModel::query()->where(['id' => $data['id']])->update($data);
        } else {
            $data['salt'] = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $password = app()->siteInfo['default_password'];
            $data['password'] = Encrypt::encryptPassword($password, $data['salt']);
            $model=new AdminModel();
            $model->loadData($data);
            $model->save();
        }
    }

    /**
     * @param $admin
     * @param $data
     * @throws \Exception
     */
    public function changePassword($admin, $data)
    {
        $update['salt'] = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $update['password'] = Encrypt::encryptPassword($data['newPassword'], $update['salt']);
        AdminModel::query()->where(['id' => $admin['id']])->update($update);
    }
}