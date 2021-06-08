<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
//$router = $app->router;
$router->get("generate", "GenerateController@index");
$router->post("generate", "GenerateController@index");

$router->group(['namespace' => 'Admin\Erp'], function ($router) {
    $router->get("erp/site-info/index", "SiteInfoController@index");
    $router->post("erp/site-info/index", "SiteInfoController@index");
    $router->get("erp/site-info/app-info", "SiteInfoController@appInfo");
    $router->post("erp/site-info/app-info", "SiteInfoController@appInfo");
    $router->get("erp/site-info/base-info", "SiteInfoController@baseInfo");
    $router->post("erp/site-info/base-info", "SiteInfoController@baseInfo");
    $router->get("erp/site-info/wechat", "SiteInfoController@wechat");
    $router->post("erp/site-info/wechat", "SiteInfoController@wechat");
});
$router->group(['middleware' => ['web'], 'namespace' => 'Admin\System'], function ($router) {
    $router->get("/system/role/index", "RoleController@index");
    $router->get("/system/role/edit-role", "RoleController@editRole");
    $router->post("/system/role/edit-role", "RoleController@editRole");
    $router->get("/system/role/set-role-admin", "RoleController@setRoleAdmin");
    $router->post("/system/role/set-role-admin", "RoleController@setRoleAdmin");
    $router->get("/system/role/set-role-menu", "RoleController@setRoleMenu");
    $router->post("/system/role/set-role-menu", "RoleController@setRoleMenu");

    $router->get("/system/admin/index", "AdminController@index");
    $router->get("/system/admin/edit-admin", "AdminController@editAdmin");
    $router->post("/system/admin/edit-admin", "AdminController@editAdmin");
    $router->get("/system/admin/set-status", "AdminController@setStatus");
    $router->post("/system/admin/set-status", "AdminController@setStatus");
    $router->post("/system/admin/reset-password", "AdminController@resetPassword");

    $router->get("/system/menu/index", "MenuController@index");
    $router->get("/system/menu/edit-menu", "MenuController@editMenu");
    $router->post("/system/menu/edit-menu", "MenuController@editMenu");

    $router->get("/system/public/login", "PublicController@login");
    $router->post("/system/public/login", "PublicController@login");
    $router->get("/system/public/captcha", "PublicController@captcha");
    $router->get("/system/admin/profile", "AdminController@profile");
    $router->post("/system/admin/change-profile", "AdminController@changeProfile");
});
