<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,maximum-scale=1.0, initial-scale=1, user-scalable=0">
    <title><?= app()->siteInfo['web_name'] ?></title>
    <?php foreach (\App\Helper\StaticHelper::$cssList as $css): ?>
    <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach; ?>
    <link rel="stylesheet" href="/static/css/login.css">
</head>
<body>
<div class="custom-bg">
    @yield('content')
</div>
</body>
<?php foreach (\App\Helper\StaticHelper::$scriptList as $script): ?>
<script src="<?= $script ?>"></script>
<?php endforeach; ?>
</html>
