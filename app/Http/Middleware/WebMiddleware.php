<?php
/**
 * 用户web端的 验证中间件
 * @author      jason
 * @copyright   (c) dms_api , Inc
 * @project     dms_api
 * @since       2021/3/29 5:53 PM
 * @version     1.0.0
 *
 */

namespace App\Http\Middleware;

use App\Helper\UrlHelper;
use App\Models\MenuModel;
use App\Models\RoleMenuModel;
use Closure;
use Illuminate\Support\Facades\Auth;

class WebMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $uri = UrlHelper::instance()->getUri();
            $module = UrlHelper::instance()->getModuleName();
            $controller = UrlHelper::instance()->getControllerName();
            $action = UrlHelper::instance()->getActionName();

            if ($this->checkNoLoginList($module . '/' . $controller, $action)) {
                return $next($request);
            }
            if (empty(Auth::user())) {
                throw new \Exception('您暂未登陆', 999);
            }
            if ($this->checkWhiteList($module . '/' . $controller, $action)) {
                return $next($request);

            }
            $menu = MenuModel::where(['url' => $uri])->first();
            if (!$menu) {
                throw new \Exception($uri . '该地址不在权限中');
            }
            if (Auth::user()->identity == 1) {
                return $next($request);
            }
            $access = RoleMenuModel::from('role_menu as a')
                ->leftJoin('role_admin as b', 'a.role_id', '=', 'b.role_id')
                ->where(['a.menu_id' => $menu['id'], 'b.admin_id' => Auth::user()->id])
                ->first();
            if (!$access) {
                throw new \Exception('您暂无该权限');
            }
        } catch (\Exception $e) {
            if (!$request->ajax() && $e->getCode() == 999) {
                return redirect('system/public/login');
            }
            throw $e;
        }
        return $next($request);
    }

    public function checkNoLoginList($moduleName, $actionName = null)
    {
        $noLoginActions = app()->config['auth.actionNoLoginList'];

        $moduleName = strtolower($moduleName);
        $actionName = strtolower($actionName);
        $_deal_action = [];
        foreach ($noLoginActions as $m => $a) {
            array_walk($a, function (&$x) {
                $x = strtolower($x);
            });
            $_deal_action[strtolower($m)] = $a;
        }
        if (isset($_deal_action[$moduleName]) && (in_array('*', $_deal_action[$moduleName]) || in_array($actionName, $_deal_action[$moduleName]))) {
            return true;
        }
        return false;
    }

    public function checkWhiteList($moduleName, $actionName = null)
    {
        $noLoginActions = app()->config['auth.actionWhiteList'];
        $moduleName = strtolower($moduleName);
        $actionName = strtolower($actionName);
        $_deal_action = [];
        foreach ($noLoginActions as $m => $a) {
            array_walk($a, function (&$x) {
                $x = strtolower($x);
            });
            $_deal_action[strtolower($m)] = $a;
        }
        if (isset($_deal_action[$moduleName]) && (in_array('*', $_deal_action[$moduleName]) || in_array($actionName, $_deal_action[$moduleName]))) {
            return true;
        }
        return false;
    }
}