@extends('layouts.main')
@section('content')
    <form class="form-box col-12 col-sm-8 col-md-6" id="save-form"
          action="<?= \App\Helper\UrlHelper::instance()->to('erp/site-info/app-info') ?>" method="post">
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">订单相关设置</label>
            <div class="col-sm-8">
                <div style="margin-bottom:1rem">订单超过 <input type="number" name="expire_order_pay" class="form-control"
                                                            placeholder="输入整数" value="<?= $model['expire_order_pay'] ?>"
                                                            style="width: 4.5rem;display: inline"> 分钟未付款，订单自动取消
                </div>
                <div style="margin-bottom:1rem">订单发货超过 <input type="number" name="expire_order_finish"
                                                              class="form-control"
                                                              placeholder="输入整数"
                                                              value="<?= $model['expire_order_finish'] ?>"
                                                              style="width: 4.5rem;display: inline"> 天，自动确认收货
                </div>
                <div style="margin-bottom:1rem">订单完成 <input type="number" name="expire_order_comment"
                                                            class="form-control"
                                                            placeholder="输入整数"
                                                            value="<?= $model['expire_order_comment'] ?>"
                                                            style="width: 4.5rem;display: inline"> 天后，自动好评
                </div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">关于我们文案</label>
            <div class="col-sm-8">
             <textarea name="about_us" cols="30" rows="3" class="form-control"
                       placeholder="请输入关于我们文案"><?= $model['about_us'] ?></textarea>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 text-nowrap col-form-label form-label">商城LOGO</label>
            <div class="col-sm-8">
                <?= \App\Helper\Input\ImageInput::instance($model['app_logo'], 'app_logo')->show(); ?>
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