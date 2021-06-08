@extends('layouts.main')
@section('content')
    <form class="form-box col-12 col-sm-8 col-md-6" id="save-form"
          action="<?= \App\Helper\UrlHelper::instance()->to('system/admin/edit-admin') ?>" method="post">
        <input type="hidden" name="id" value="<?= !empty($model['id']) ? $model['id'] : '' ?>">
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">用户名</label>
            <div class="col-sm-8">
                <input type="text" name="username" class="form-control"
                       value="<?= isset($model['username']) ? $model['username'] : '' ?>" placeholder="请输入用户名">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">真实姓名</label>
            <div class="col-sm-8">
                <input type="text" name="realname" class="form-control"
                       value="<?= isset($model['realname']) ? $model['realname'] : '' ?>" placeholder="请输入真实姓名">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">邮箱</label>
            <div class="col-sm-8">
                <input type="text" name="email" class="form-control"
                       value="<?= isset($model['email']) ? $model['email'] : '' ?>" placeholder="请输入邮箱">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">手机号</label>
            <div class="col-sm-8">
                <input type="text" name="mobile" class="form-control"
                       value="<?= isset($model['mobile']) ? $model['mobile'] : '' ?>" placeholder="请输入手机号">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">头像</label>
            <div class="col-sm-8">
                <?= \App\Helper\Input\ImageInput::instance($model['avatar'], 'avatar')->show(); ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="offset-4 col-sm-8">
                <input class="btn btn-primary btn-lg" type="submit" value="保存"/>
            </div>
        </div>
    </form>
@endsection
<?php \App\Helper\StaticHelper::appendScript('admin.js') ?>