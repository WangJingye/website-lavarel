<?php

namespace App\Http\Controllers\Admin\Common;

use App\Models\SiteInfoModel;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /** @var Request */
    public $request;

    public function __construct(Request $request)
    {
        app()->siteInfo = SiteInfoModel::first();
        $this->request = $request;
    }


    /**
     * 渲染界面
     * @param $url
     * @param $params
     */
    public function render($url, $params = [])
    {
        return view($url, $params);
    }

    public function success($message = '', $data = [])
    {
        return [
            'code' => 200,
            'message' => $message,
            'data' => empty($data) ? null : $data,
        ];
    }

    public function error($message = '', $data = [], $code = 400)
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => empty($data) ? null : $data,
        ];
    }
}
