<?php

namespace App\Lib\Common;

class Encrypt
{

    public static function encryptPassword($password, $salt)
    {
        return md5($salt . md5($password . $salt));
    }

    public static function generateToken()
    {
        return md5(time() . str_pad(rand(0, 10000), 5, 0, STR_PAD_LEFT));
    }

}