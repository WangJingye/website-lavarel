@extends('layouts.main')
@section('content')
    <form class="form-box col-12 col-sm-8 col-md-6" id="save-form"
          action="<?= \App\Helper\UrlHelper::instance()->to('erp/site-info/base-info') ?>" method="post">
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">管理后台名称</label>
            <div class="col-sm-8">
                <input type="text" name="web_name" class="form-control" value="<?= $model['web_name'] ?>"
                       placeholder="管理后台名称">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">网站域名</label>
            <div class="col-sm-8">
                <input type="text" name="web_host" class="form-control" value="<?= $model['web_host'] ?>"
                       placeholder="请输入网站域名">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">网站IP</label>
            <div class="col-sm-8">
                <input type="text" name="web_ip" class="form-control" value="<?= $model['web_ip'] ?>"
                       placeholder="请输入网站IP">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">用户初始密码</label>
            <div class="col-sm-8">
                <input type="password" name="default_password" class="form-control"
                       value="<?= $model['default_password'] ?>"
                       placeholder="请输入用户初始密码">
            </div>
        </div>
        <div class="form-group row">
            <div class="offset-4 col-sm-8">
                <input class="btn btn-primary btn-lg" type="submit" value="保存"/>
            </div>
        </div>
    </form>
@endsection
<?php \App\Helper\StaticHelper::appendScript('site-info.js') ?>