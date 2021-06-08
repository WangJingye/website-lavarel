<?php
/**
 * User: thomas
 * Date: 2021/6/7
 * Email: <thomas.wang@heavengifts.com>
 */

namespace App\Helper;

class UploadHelper
{
    public $path;

    protected static $instance;

    public function __construct()
    {
        $this->path = app()->basePath() . '/' . 'public' . '/';
    }

    /**
     * @return UploadHelper
     */
    public static function instance()
    {
        if (static::$instance == null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function parseFileOrUrl($key, $path = '/')
    {
        $res = [];
        //如果是common目录下的文件需要移动到对应目录
        if ($urlList = request()->get($key)) {
            if (!is_array($urlList)) {
                $urlList = explode(',', $urlList);
            }

            $baseUrl = app()->siteInfo['web_host'];
            foreach ($urlList as $i => $url) {
                if (strpos($url, $baseUrl . '/upload/common') === 0) {
                    $filename = str_replace($baseUrl . '/upload/common/', '', $url);
                    $oldFilePath = $this->path . 'upload/common/';
                    $oldFilename = $oldFilePath . $filename;
                    $newFilePath = $this->path . 'upload/' . $path;
                    $newFilename = $newFilePath . $filename;
                    if (!file_exists($newFilePath)) {
                        mkdir($newFilePath, 0755, true);
                    }
                    if (file_exists($oldFilename)) {
                        copy($oldFilename, $newFilename);
                        unlink($oldFilename);
                    }
                    $res[$i] = $baseUrl . '/upload/' . $path . $filename;
                } else {
                    $res[$i] = $url;
                }
            }
        }
        $path = trim($path, '/') != '' ? trim($path, '/') . '/' : '';
        if (!empty($_FILES[$key])) {
            $files = $this->parseFile($_FILES[$key], $path);
            foreach ($files as $i => $new) {
                $res[$i] = $new;
            }
        }
        if (!empty($_FILES[$key . '_add'])) {
            $files = $this->parseFile($_FILES[$key . '_add'], $path);
            foreach ($files as $i => $new) {
                $res[($i + 1000)] = $new;
            }
        }
        if (count($res)) {
            ksort($res);
        }
        return implode(',', $res);
    }

    /**
     * 文件上传处理
     * @param $file
     * @param bool $is_image
     * @return array
     * @throws \Exception
     */
    public function parseFile($file, $path = '/')
    {
        $ext_arr = ['gif', 'jpg', 'jpeg', 'png', 'bmp', 'mp4'];
        $fileList = $file['name'];
        if (is_string($file['name'])) {
            $fileList = [$file['name']];
        }
        $tmpList = $file['tmp_name'];
        if (is_string($file['tmp_name'])) {
            $tmpList = [$file['tmp_name']];
        }
        $res = [];
        $path = trim($path, '/') != '' ? trim($path, '/') . '/' : '';
        foreach ($fileList as $i => $f_name) {
            if (!$f_name) {
                continue;
            }
            $arr = explode('.', $f_name);
            $ext = end($arr);
            if (!in_array($ext, $ext_arr)) {
                throw new \Exception('不允许的文件类型,只支持' . implode('/', $ext_arr));
            }
            $filePath = $this->path . 'upload/' . $path;
            if (!file_exists($filePath)) {
                mkdir($filePath, 0755, true);
            }
            $filename = 'upload/' . $path . md5_file($tmpList[$i]) . '.' . $ext;
            if (!file_exists($this->path . $filename)) {
                if (@!move_uploaded_file($tmpList[$i], $this->path . $filename)) {
                    throw new \Exception('文件保存失败');
                }
            }
            $res[$i] = app()->siteInfo['web_host'] . '/' . $filename;
        }
        return $res;
    }
}
