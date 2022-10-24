<?php
/**
 * User: thomas
 * Date: 2021/6/4
 * Email: <thomas.wang@heavengifts.com>
 */

namespace App\Helper;

use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\UrlGenerator;

class UrlHelper extends UrlGenerator
{
    public static $instance;

    /**
     * @return UrlHelper
     */
    public static function instance()
    {
        if (static::$instance === null) {
            static::$instance = new static(app());
        }
        return static::$instance;
    }

    public function __construct(Application $app)
    {
        return parent::__construct($app);
    }

    public function to($path, $extra = [], $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }
        $scheme = $this->getSchemeForUrl($secure);

        $extra = $this->formatParameters($extra);
        $tails = [];
        foreach ($extra as $k => $v) {
            $tails[] = rawurlencode($k) . '=' . rawurlencode($v);
        }
        $tail = implode('&', $tails);
        $root = $this->getRootUrl($scheme);

        return $this->trimUrl($root, $path, $tail);
    }

    /**
     * Format the given URL segments into a single URL.
     *
     * @param string $root
     * @param string $path
     * @param string $tail
     * @return string
     */
    protected function trimUrl($root, $path, $tail = '')
    {
        return trim($root . '/' . trim($path . '?' . $tail, '?'), '/');
    }

    public function getModuleName()
    {
        $arr = explode('/', $this->getUri());
        if (count($arr) != 3) {
            return 'app';
        }
        return $arr[0];
    }

    public function getControllerName()
    {
        $arr = explode('/', $this->getUri());
        if (count($arr) != 3) {
            array_unshift($arr, 'app');
        }
        return $arr[1];
    }

    public function getActionName()
    {
        $url = $this->getUri();
        if (empty($url) || $url == '/') {
            $url = 'erp/site-info/index';
        }
        $arr = explode('/', $url);
        if (count($arr) != 3) {
            array_unshift($arr, 'app');
        }

        return $arr[2];
    }

    public function getUri()
    {
        $uri = request()->getRequestUri();
        $arr = explode('?', $uri);
        $uri = trim($arr[0], '/');
        return $uri;
    }
}