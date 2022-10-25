<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,maximum-scale=1.0, initial-scale=1, user-scalable=0">
    <title><?= app()->siteInfo['web_name'] ?></title>
    <?php foreach (\App\Helper\StaticHelper::$cssList as $css): ?>
    <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach; ?>
</head>
<body style="background-color: #f6f8f9;">
<?php
$menuService = new \App\Lib\MenuService();
$currentMenu = $menuService->getCurrentMenu();
$activeMenuList = $menuService->getActiveMenu();
$topList = $menuService->getTopList();
$leftList = $menuService->getLeftList();
$breadcrumbs = [];
$arr = array_reverse($activeMenuList);
foreach ($arr as $v) {
    $tmp = ['name' => $v['name']];
    $tmp['url'] = $v['url'] != '' ? $v['url'] : '';
    $breadcrumbs[] = $tmp;
}
?>
<header class="navbar navbar-expand-lg navbar-dark bd-navbar">
    <div class="col-9 col-md-3 col-xl-2">
        <div style="color:#fff;text-align: center;font-size: 1.2rem;"><?=app()->siteInfo['web_name']?></div>
    </div>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#top-menu-list"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="top-menu-list" style="text-align: center">
        <div class="navbar-nav">
            <?php foreach ($topList as $v): ?>
            <a class="nav-item nav-link <?= isset($activeMenuList[$v['id']]) ? 'active' : '' ?>"
               href="<?= $v['url'] != '' ? \App\Helper\UrlHelper::instance()->to($v['url']) : 'javascript:void(0)' ?>">
                <span><i class="<?= $v['icon'] ?>"></i> <?= $v['name'] ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <ul class="navbar-nav" style="margin-right: 2rem">
        <li class="nav-item dropdown">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#left-menu-list"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="nav-link dropdown-toggle" style="display:inline;" href="javascript:void(0)" role="button"
               data-toggle="dropdown">
                <?php if (\Illuminate\Support\Facades\Auth::user()->avatar): ?>
                <img class="rounded-circle" src="<?=\Illuminate\Support\Facades\Auth::user()->avatar?>"
                     style="width:30px;height:30px">
                <?php endif; ?>
                <span><?=\Illuminate\Support\Facades\Auth::user()->realname ?></span>
            </a>
            <div class="dropdown-menu" style="position: absolute">
                <a class="dropdown-item"
                   href="<?= \App\Helper\UrlHelper::instance()->to('system/admin/profile') ?>">个人信息</a>
                <a class="dropdown-item"
                   href="<?= \App\Helper\UrlHelper::instance()->to('system/public/logout') ?>">登出</a>
            </div>
        </li>
    </ul>
</header>
<div class="row flex-xl-nowrap" style="margin:0">
    <div class="col-12 col-md-3 col-xl-2 bd-sidebar collapse" id="left-menu-list" style="padding: 0">
        <ul class="list-group list-group-flush bd-links">
            <?php foreach ($leftList as $v): ?>
            <li class="list-group-item main-item<?= isset($activeMenuList[$v['item']['id']]) ? ' active' : '' ?>"<?= $v['item']['url'] ? ' data-url="' . \App\Helper\UrlHelper::instance()->to($v['item']['url']) . '"' : '' ?>>
                <div><i class="<?= $v['item']['icon'] ?>"></i> <?= $v['item']['name'] ?></div>
            </li>
            <?php if (isset($v['list']) && count($v['list'])): ?>
            <li class="list-group-item sub-item collapse <?= isset($activeMenuList[$v['item']['id']]) ? 'show' : '' ?>"
                style="border-top: 0">
                <ul class="list-sub-item">
                    <?php foreach ($v['list'] as $child): ?>
                    <li class="list-group-item <?= isset($activeMenuList[$child['id']]) ? 'active' : '' ?>"
                        data-url="<?= \App\Helper\UrlHelper::instance()->to($child['url']) ?>"><?= $child['name'] ?></li>
                    <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="col-12 col-md-9 col-xl-10 bd-content" style="padding: 0">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="border-radius: 0;margin-bottom: 0;padding: 0.3rem 1rem;background: #fff">
                <li class="breadcrumb-item">
                    <a href="<?= \App\Helper\UrlHelper::instance()->to('/') ?>">
                        <i class="glyphicon glyphicon-home"></i> 主页</a>
                </li>
                <?php foreach ($breadcrumbs as $key => $v): ?>
                <?php if ($key == count($breadcrumbs) - 1) {
                    $v['url'] = '';
                    if (!empty($title)) {
                        $v['name'] = $title;
                    }
                } ?>
                <li class="breadcrumb-item <?= $v['url'] == '' ? 'active' : '' ?>">
                    <a <?= $v['url'] != '' ? 'href="' . \App\Helper\UrlHelper::instance()->to($v['url']) . '"' : ''; ?>><?= $v['name'] ?></a>
                </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <div class="bd-container">
            <div style="background: #fff;border-radius: 0.3rem;padding: 1rem;min-height: calc(100vh - 7rem)">
                <?php if($currentMenu['show_title'] == 1):?>
                <h3><?= !empty($title) ? $title : $currentMenu['name'] ?></h3>
                <hr>
                <?php endif;?>
                @yield('content')
            </div>
        </div>
    </div>
</div>
</body>
<?php foreach (\App\Helper\StaticHelper::$scriptList as $script): ?>
<script src="<?= $script ?>"></script>
<?php endforeach; ?>
<script>
    document.documentElement.addEventListener('touchstart', function (event) {
        if (event.touches.length > 1) {
            event.preventDefault();
        }
    }, false);
</script>
</html>