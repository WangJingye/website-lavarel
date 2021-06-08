<?php
/**
 * User: thomas
 * Date: 2021/6/7
 * Email: <thomas.wang@heavengifts.com>
 */
namespace App\Helper;

class StaticHelper{
    
    public static $scriptList = [
        '/static/js/jquery.js',
        '/static/plugin/bootstrap/js/popper.min.js',
        '/static/plugin/bootstrap/js/bootstrap.js',
        '/static/plugin/lightbox/js/lightbox.min.js',
        '/static/js/jquery.validate.js',
        '/static/js/select2.min.js',
        '/static/js/popup.js',
        '/static/js/ztree.core.js',
        '/static/js/ztree.excheck.js',
        '/static/plugin/kindeditor/kindeditor-min.js',
        '/static/plugin/kindeditor/lang/zh_CN.js',
        '/static/js/main.js',
    ];

    /**
     * 界面css
     * @var array
     */
    public static $cssList = [
        '/static/css/iconfont.css',
        '/static/plugin/lightbox/css/lightbox.css',
        '/static/plugin/bootstrap/css/bootstrap.css',
        '/static/plugin/bootstrap/css/fonts.css',
        '/static/css/select2.css',
        '/static/css/ztree.css',
        '/static/css/main.css',
    ];

    public static function appendScript($script)
    {
        if (strpos($script, '/') !== 0) {

            $script = '/static/js/admin/' . UrlHelper::instance()->getModuleName() . '/' . $script;
        }
        if (!in_array($script, static::$scriptList)) {
            static::$scriptList[] = $script;
        }
    }

    public static function appendCss($css)
    {
        if (strpos($css, '/') !== 0) {
            $css = '/static/' . $css;
        }
        if (!in_array($css, static::$cssList)) {
            static::$cssList[] = $css;
        }
    }
}