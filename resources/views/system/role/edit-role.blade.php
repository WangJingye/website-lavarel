@extends('layouts.main')
@section('content')
    <form class="form-box col-12 col-sm-8 col-md-6" id="save-form"
          action="<?= \App\Helper\UrlHelper::instance()->to('system/role/edit-role') ?>" method="post">
        <input type="hidden" name="id" value="<?= $model['id'] ?? '' ?>">
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">角色名称</label>
            <div class="col-sm-8">
                <input type="text" name="name" class="form-control"
                       value="<?= $model['name'] ?? '' ?>" placeholder="请输入角色名称">
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">描述</label>
            <div class="col-sm-8">
            <textarea name="desc"
                      class="form-control"><?= $model['desc'] ?? '' ?></textarea>
            </div>
        </div>
        <div class="form-group row">
            <div class="offset-4 col-sm-8">
                <input class="btn btn-primary btn-lg" type="submit" value="保存"/>
            </div>
        </div>
    </form>
@endsection
<?php \App\Helper\StaticHelper::appendScript('role.js') ?>