<?php
/**
 * User: thomas
 * Date: 2021/6/7
 * Email: <thomas.wang@heavengifts.com>
 */

namespace App\Helper;

use Illuminate\Support\Facades\Session;

class SessionHelper
{
    public static function get($key, $default = '')
    {
        return Session::get($key);
    }

    public static function set($key, $value = null)
    {
        if ($value == null) {
            Session::forget($key);
        } else {
            Session::put($key, $value);
        }
        Session::save();
    }

    public static function remove($key)
    {
        Session::forget($key);
        Session::save();
    }
}