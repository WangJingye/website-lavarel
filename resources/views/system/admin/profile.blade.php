@extends('layouts.main')
@section('content')
    <nav>
    <div class="nav nav-tabs profile-nav" id="nav-tab" role="tablist">
        <a class="nav-item nav-link active" data-toggle="tab" href="#nav-profile" role="tab">个人信息</a>
        <a class="nav-item nav-link" data-toggle="tab" href="#nav-password" role="tab">修改密码</a>
    </div>
</nav>
<div class="tab-content bd-tab-content">
    <div class="tab-pane fade show active" id="nav-profile" role="tabpanel">
        <form id="change-user-info-form" class="form-box col-12 col-sm-8 col-md-6"
              action="<?= \App\Helper\UrlHelper::instance()->to('system/admin/change-profile') ?>" method="post">
            <div class="form-group row">
                <label class="col-sm-4 text-nowrap col-form-label form-label">用户名</label>
                <div class="col-sm-8">
                    <span class="form-control-plaintext"><?= \Illuminate\Support\Facades\Auth::user()['username'] ?></span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 text-nowrap col-form-label form-label">真实姓名</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="realname" value="<?= \Illuminate\Support\Facades\Auth::user()['realname'] ?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 text-nowrap col-form-label form-label">邮箱地址</label>
                <div class="col-sm-8">
                    <input type="email" class="form-control" name="email" value="<?= \Illuminate\Support\Facades\Auth::user()['email'] ?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 text-nowrap col-form-label form-label">联系电话</label>
                <div class="col-sm-8">
                    <input type="tel" class="form-control" name="mobile" value="<?= \Illuminate\Support\Facades\Auth::user()['mobile'] ?>">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 text-nowrap col-form-label form-label">个人头像</label>
                <div class="col-sm-8">
                    <?= \App\Helper\Input\ImageInput::instance(\Illuminate\Support\Facades\Auth::user()['avatar'], 'avatar')->show(); ?>
                </div>
            </div>
            <div class="form-group row">
                <div class="offset-4 col-sm-8 text-nowrap">
                    <input class="btn btn-primary btn-lg" type="submit" value="保存"/>
                </div>
            </div>
        </form>
    </div>
    <div class="tab-pane fade" id="nav-password" role="tabpanel">
        <form id="change-password-form" class="form-box col-12 col-sm-8 col-md-6"
              action="<?= \App\Helper\UrlHelper::instance()->to('system/admin/change-password') ?>" method="post">
            <div class="form-group row">
                <label class="col-sm-4 text-nowrap col-form-label form-label">当前登录密码</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" name="password">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 text-nowrap col-form-label form-label">新登录密码</label>
                <div class="col-sm-8">
                    <input type="password" id="newPassword" class="form-control" name="newPassword">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 text-nowrap col-form-label form-label">确认新登录密码</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" name="rePassword">
                </div>
            </div>
            <div class="form-group row">
                <div class="offset-4 col-sm-8 text-nowrap">
                    <input class="btn btn-primary btn-lg" type="submit" value="保存"/>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
<?php \App\Helper\StaticHelper::appendScript('admin.js') ?>
