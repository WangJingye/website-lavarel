<?php
/**
 * User: thomas
 * Date: 2021/6/8
 * Email: <thomas.wang@heavengifts.com>
 */

namespace App\Http\Controllers\Admin\Erp;

use App\Helper\UploadHelper;
use App\Http\Controllers\Admin\Common\Controller;
use App\Models\SiteInfoModel;

class SiteInfoController extends Controller
{
    /**
     * @throws \Exception
     */
    public function wechat()
    {
        $params = $this->request;
        $siteInfo = app()->siteInfo;
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                if (!$siteInfo) {
                    $siteInfo = new SiteInfoModel();
                }
                $siteInfo->loadData($params->request->all());
                $siteInfo->save();
                return $this->success('保存成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
        return $this->render('erp/site-info/wechat', [
            'model' => $siteInfo,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function baseInfo()
    {
        $params = $this->request;
        $siteInfo = app()->siteInfo;
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                if (!$siteInfo) {
                    $siteInfo = new SiteInfoModel();
                }
                $siteInfo->loadData($params->request->all());
                $siteInfo->save();
                return $this->success('保存成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
        return $this->render('erp/site-info/base-info', [
            'model' => $siteInfo,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function appInfo()
    {
        $params = $this->request;
        $siteInfo = app()->siteInfo;
        if ($this->request->ajax() && $this->request->isMethod('POST')) {
            try {
                $params['app_logo'] = UploadHelper::instance()->parseFileOrUrl('app_logo', 'erp/site-info');
                if (!$siteInfo) {
                    $siteInfo = new SiteInfoModel();
                }
                $siteInfo->loadData($params->request->all());
                $siteInfo->save();
                return $this->success('保存成功');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
        return $this->render('erp/site-info/app-info', [
            'model' => $siteInfo,
        ]);
    }

    /**
     * 首页
     * @throws \Exception
     */
    public function index()
    {
        return $this->render('erp/site-info/index');
    }
}