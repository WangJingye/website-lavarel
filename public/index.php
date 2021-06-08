<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| First we need to get an application instance. This creates an instance
| of the application / container and bootstraps the application so it
| is ready to receive HTTP / Console requests from the environment.
|
*/

$app = require __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

if(in_array(env('APP_ENV'), ['test','local'])){
    header('Access-Control-Allow-Origin: *');
} else {
    $allow_origin = array(
    );

    $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';  //跨域
    header('Access-Control-Allow-Origin: '. $origin);
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods:GET,HEAD,PUT,POST,DELETE,PATCH,OPTIONS");
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit;
}
header('Access-Control-Allow-Methods:GET,HEAD,PUT,POST,DELETE,PATCH,OPTIONS'); // 允许请求的类型
header('Access-Control-Allow-Headers: device,uuid,login,timestamp,lang,version,serial-number,company,phone-model,system-version,token,appkey,appsecret,Content-Type,accessToken,Content-Length,Accept-Encoding,X-Requested-with, Origin,DNT,X-CustomHeader,Keep-Alive,User-Agent,If-Modified-Since,Cache-Control,Pragma,Referer,Host,Connection');


$app->run();
