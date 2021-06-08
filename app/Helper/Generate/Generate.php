<?php

namespace App\Helper\Generate;

use App\Helper\ObjectAccess;
use Illuminate\Support\Facades\DB;

class Generate extends ObjectAccess
{
    public static $instance;
    public $table;
    public $controllerUrl;
    public $module;
    public $theme;
    public $primaryKey;
    public $option;
    public $app;
    public $columnTypes;
    public $uniqueColumns = [];
    public $templatePath = '';
    public $prefix = '';

    /**
     * Generate constructor.
     * @param $app
     * @param $module
     * @param $table
     * @throws \Exception
     */
    public function __construct($option)
    {
        $this->prefix = app()->config['database.connections.mysql.prefix'];
        $this->theme = $option['template'];
        $this->templatePath = app()->path() . '/Helper/Generate/template/';
        $this->app = 'Admin';
        $this->table = ucfirst($option['table']);
        $this->module = ucfirst($option['module']);
        $this->controllerUrl = strtolower(trim(preg_replace('/([A-Z])/', '-$1', $this->table), '-'));
        $sql = 'show keys from ' . strtolower($this->prefix . $option['table']) . ';';
        $keys = DB::select($sql);
        $keys = json_decode(json_encode($keys), true);
        $this->option = $option;
        $uniqueColumns = [];
        foreach ($keys as $v) {
            //主键单独处理
            if ($v['Key_name'] == 'PRIMARY') {
                $this->primaryKey = $v['Column_name'];
                continue;
            }
            //不允许重复
            if ($v['Non_unique'] == 0) {
                $uniqueColumns[$v['Key_name']][] = $v['Column_name'];
            }
        }
        $columnTypes = [];
        $sql = 'show columns from ' . strtolower($this->prefix . $option['table']) . ';';
        $fields = DB::select($sql);
        $fields = json_decode(json_encode($fields), true);
        $fields = array_column($fields, null, 'Field');
        foreach ($fields as $field => $v) {
            $type = [];
            if (strpos($v['Type'], 'int') !== false) {
                $type = 'int';
            } elseif (strpos($v['Type'], 'char') !== false || strpos($v['Type'], 'ext') !== false) {
                $type = 'string';
            } elseif (strpos($v['Type'], 'float') !== false ||
                strpos($v['Type'], 'float') !== false ||
                strpos($v['Type'], 'double') !== false ||
                strpos($v['Type'], 'decimal') !== false
            ) {
                $type = 'float';
            }
            $columnTypes[$field] = $type;
        }
        $this->columnTypes = $columnTypes;
        $this->uniqueColumns = $uniqueColumns;
    }

    /**
     * @param $app
     * @param $module
     * @param $table
     * @return Generate
     * @throws \Exception
     */
    public static function instance($option)
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($option);
        }
        return self::$instance;
    }

    public function run()
    {
        if ($this->theme == 'web') {
            $this->common()->service()->controller()->view()->js()->route();
        } else {
            $this->common()->service()->controller();
        }
    }

    public function route()
    {
        $webStr = file_get_contents(app()->basePath() . '/routes/web.php');
        $prefixUri = lcfirst($this->module) . '/' . lcfirst($this->table) . '/';
        $list = [
            $prefixUri . 'index' => ['type' => ['get'], 'action' => 'index'],
            $prefixUri . 'edit' => ['type' => ['get', 'post'], 'action' => 'edit'],


            $prefixUri . 'delete' => ['type' => ['post'], 'action' => 'delete'],
        ];
        if (isset($this->option['fcomment']['status'])) {
            $list[$prefixUri . 'set-status'] = ['type' => ['post'], 'action' => 'setStatus'];
        }
        if (isset($this->option['fcomment']['sort'])) {
            $list[$prefixUri . 'set-sort'] = ['type' => ['post'], 'action' => 'setSort'];
        }
        $str = '    $router->%s(\'%s\', "' . $this->table . 'Controller@%s");';
        $routeList = [];
        foreach ($list as $k => $v) {
            foreach ($v['type'] as $type) {
                $routeStr = sprintf($str, $type, $k, $v['action']);
                if (strpos($webStr, $routeStr) !== false) {
                    continue;
                }
                $routeList[] = $routeStr;
            }
        }
        if (count($routeList)) {
            $str = preg_replace('/(\'namespace\' => \'' . $this->app . '\\\\' . $this->module . '\'.*?\n)/', '$1' . implode(PHP_EOL, $routeList) . PHP_EOL, $webStr);
            file_put_contents(app()->basePath() . '/routes/web.php', $str);
        }
    }

    public function model()
    {
        $filename = app()->path() . '/Models/' . $this->table . 'Model.php';
        if (!file_exists($filename)) {
            $file = $this->templatePath . 'model';
            $str = file_get_contents($file);
            $str = str_replace('{{Table}}', $this->table, $str);
            $str = str_replace('{{table}}', strtolower($this->table), $str);
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function js()
    {
        $dir = app()->basePath() . '/public/static/js/' . lcfirst($this->app) . '/' . lcfirst($this->module);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        //js
        $rules = '';
        $rulesMessage = '';
        foreach ($this->option['fcomment'] as $field => $label) {
            if ($field == $this->primaryKey) {
                continue;
            }
            if (!isset($this->option['frequire'][$field]) || $this->option['frequire'][$field] == 0) {
                continue;
            }
            if ($rules) {
                $rules .= ',';
                $rulesMessage .= ',';
            }
            $rules .= PHP_EOL . '            ' . $field . ': {';
            $rulesMessage .= PHP_EOL . '            ' . $field . ': {';
            $rules .= PHP_EOL . '                required: true';
            $rulesMessage .= PHP_EOL . '                required: \'请输入' . $label . '\'';
            $rules .= PHP_EOL . '            }';
            $rulesMessage .= PHP_EOL . '            }';

        }
        $statusJs = '';
        if (isset($this->option['fcomment']['status']) && $this->option['fchoice']['status'] == 1 && count(($statusList = $this->getChooseList('status')['list'])) == 2) {
            $statusPartJs = '';
            foreach ($statusList as $k => $v) {
                $statusPartJs .= '
                if (args.status == ' . $k . ') {
                    data = {
                        \'btn_class\': \'' . ($k == 0 ? 'btn-success' : 'btn-danger') . '\',
                        \'class_name\': \'' . ($k == 0 ? 'glyphicon-ok-circle' : 'glyphicon-remove-circle') . '\',
                        \'status\': \'' . ($k == 0 ? 1 : 0) . '\',
                        \'name\': \'' . ($k == 0 ? '启用' : '禁用') . '\',
                        \'title\': \'' . $v . '\',
                    };
                }';
            }
            $statusJs = PHP_EOL . '    $(\'.set-status-btn\').click(function () {
        let $this = $(this);
        let tr = $(this).parents(\'tr\');
        let args = {
            id: $this.data(\'id\'),
            status: $this.data(\'status\')
        };
        $.loading(\'show\');
        $.post($this.data(\'url\'), args, function (res) {
            $.loading(\'hide\');
            if (res.code == 200) {
                $.success(res.message);' . $statusPartJs . '
                tr.find(\'.status\').html(data.title);
                $this.data(\'status\', data.status);
                $this.removeClass(\'btn-success\').removeClass(\'btn-danger\').addClass(data.btn_class);
                $this.find(\'.glyphicon\').removeClass(\'glyphicon-remove-circle\').removeClass(\'glyphicon-ok-circle\').addClass(data.class_name);
                $this.find(\'span\').html(data.name);
            } else {
                $.error(res.message);
            }
        }, \'json\');
    });';
        }
        $sortJs = '';
        if (isset($this->option['fcomment']['sort'])) {
            $sortJs = PHP_EOL . '    $(\'.set-sort-btn\').click(function () {
        var $this = $(this);
        var html = \'<form><div class="form-group row">\' +
            \'<label for="sort-set" class="col-sm-3 col-form-label">设置排序</label>\' +
            \'<div class="col-sm-9">\' +
            \'<input type="number" class="form-control" id="sort-set">\' +
            \'</div>\' +
            \'</div></form>\';
        $.showModal({
            title: \'设置排序\', content: html, width: \'30vw\', okCallback: function () {
                var args = {
                    id: $this.data(\'id\'),
                    sort: $(\'#modal-event\').find(\'#sort-set\').val()
                };
                if (!args.sort.length) {
                    $.error(\'请输入排序值\');
                    return false;
                }
                $.loading(\'show\');
                $.post($this.data(\'url\'), args, function (res) {
                    $.loading(\'hide\');
                    if (res.code == 200) {
                        $.success(res.message);
                        $this.parents(\'tr\').find(\'.sort\').html(args.sort);
                    } else {
                        $.error(res.message);
                    }
                }, \'json\')
            }
        })
    });';
        }
        $filename = $dir . '/' . $this->controllerUrl . '.js';
        $file = $this->templatePath . 'js';
        $str = file_get_contents($file);
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{controllerUrl}}', $this->controllerUrl, $str);
        $str = str_replace('{{mtable}}', lcfirst($this->table), $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{mmodule}}', lcfirst($this->module), $str);
        $str = str_replace('{{rules}}', $rules, $str);
        $str = str_replace('{{rulesMessage}}', $rulesMessage, $str);
        $str = str_replace('{{statusJs}}', $statusJs, $str);
        $str = str_replace('{{sortJs}}', $sortJs, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);

        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function view()
    {
        $dir = app()->basePath() . '/resources/views/' . lcfirst($this->module) . '/' . $this->controllerUrl;
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        //edit
        $inputParams = '';
        foreach ($this->option['fcomment'] as $field => $label) {
            if ($field == $this->primaryKey) {
                continue;
            }
            if (!isset($this->option['feditshow'][$field]) || $this->option['feditshow'][$field] == 0) {
                continue;
            }
            $inputParams .= PHP_EOL . '        <div class="form-group row">';
            $inputParams .= PHP_EOL . '            <label class="col-sm-4 text-nowrap col-form-label form-label">' . $label . '</label>';
            $inputParams .= PHP_EOL . '            <div class="col-sm-8">';
            if (in_array($this->option['ftype'][$field], ['select', 'select2', 'radio', 'checkbox'])) {
                $res = $this->getChooseList($field);
            }
            if (in_array($this->option['ftype'][$field], ['select', 'select2', 'radio', 'checkbox'])) {
                $inputParams .= PHP_EOL . '                <?= \App\Helper\Input\SelectInput::instance($' . $res['variable'] . ', $model[\'' . $field . '\'], \'' . $field . '\', \'' . $this->option['ftype'][$field] . '\')->show(); ?>';
            } else if ($this->option['ftype'][$field] == 'textarea') {
                $inputParams .= PHP_EOL . '                <textarea name="' . $field . '" class="form-control" placeholder="请输入' . $label . '"><?= $model[\'' . $field . '\'] ?></textarea>';
            } else if ($this->option['ftype'][$field] == 'image') {
                $inputParams .= PHP_EOL . '                <?= \App\Helper\Input\ImageInput::instance($model[\'' . $field . '\'], \'' . $field . '\', 9)->show(); ?>';;
            } else {
                $placeholder = '请输入' . $label;
                if (in_array($this->option['ftype'][$field], ['date', 'date-normal', 'datetime', 'datetime-normal'])) {
                    $placeholder = $label . '，格式为2019-01-01';
                } else if (in_array($this->option['ftype'][$field], ['datetime', 'datetime-normal'])) {
                    $placeholder = $label . '，格式为2019-01-01 09:00:00';
                }
                $inputParams .= PHP_EOL . '                <input type="text" name="' . $field . '" class="form-control" value="<?= $model[\'' . $field . '\']?>" placeholder="' . $placeholder . '">';
            }
            $inputParams .= PHP_EOL . '            </div>';
            $inputParams .= PHP_EOL . '        </div>';
        }
        $filename = $dir . '/edit.blade.php';
        $file = $this->templatePath . 'view/edit';
        $str = file_get_contents($file);
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{controllerUrl}}', $this->controllerUrl, $str);
        $str = str_replace('{{mtable}}', lcfirst($this->table), $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{mmodule}}', lcfirst($this->module), $str);
        $str = str_replace('{{inputParams}}', $inputParams, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);

        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        //index
        $searchs = [];
        $searchPer = '';
        $header = '';
        $body = '';
        foreach ($this->option['fcomment'] as $field => $label) {
            $res = $this->getChooseList($field);
            if (isset($this->option['fpageshow'][$field]) && $this->option['fpageshow'][$field] == 1) {
                $header .= PHP_EOL . '                <th>' . $label . '</th>';
                $statusHtml = '';
                if ($field == 'status' && $this->option['fchoice'][$field] == 1 && count($res['list']) == 2) {
                    $statusHtml = ' class="status"';
                }
                if ($field == 'sort') {
                    $statusHtml = ' class="sort"';
                }
                if (in_array($this->option['ftype'][$field], ['select', 'radio', 'checkbox'])) {
                    $body .= PHP_EOL . '                <td' . $statusHtml . '><?= $' . $res['variable'] . '[$v[\'' . $field . '\']] ?></td>';
                } else if ($this->option['ftype'][$field] == 'date') {
                    $body .= PHP_EOL . '                <td' . $statusHtml . '><?= date(\'Y-m-d\', $v[\'' . $field . '\']) ?></td>';

                } else if ($this->option['ftype'][$field] == 'datetime') {
                    $body .= PHP_EOL . '                <td' . $statusHtml . '><?= date(\'Y-m-d H:i:s\', $v[\'' . $field . '\']) ?></td>';
                } else if ($this->option['ftype'][$field] == 'image') {
                    $body .= PHP_EOL . '                <td' . $statusHtml . '>';
                    $body .= PHP_EOL . '                    <?php if ($v[\'' . $field . '\']): ?>';
                    $body .= PHP_EOL . '                        <img src="<?= $v[\'' . $field . '\'] ?>" style="width: 60px;height: 60px;">';
                    $body .= PHP_EOL . '                    <?php endif; ?>';
                    $body .= PHP_EOL . '                </td' . $statusHtml . '>';
                } else {
                    $body .= PHP_EOL . '                <td' . $statusHtml . '><?= $v[\'' . $field . '\'] ?></td>';
                }
            }
            if (isset($this->option['fpagesearch1'][$field]) && $this->option['fpagesearch1'][$field] == 1) {
                if (in_array($this->option['ftype'][$field], ['date', 'datetime'])) {
                    $searchPer .= PHP_EOL . '        <div class="form-content">';
                    $searchPer .= PHP_EOL . '            <?= \App\Helper\Input\TimeSearch::instance(\'' . $field . '\', \'' . $label . '\', $params)->show() ?>';
                    $searchPer .= PHP_EOL . '        </div>';
                } else {
                    $searchPer .= PHP_EOL . '        <div class="form-content">';
                    $searchPer .= PHP_EOL . '            <span class="col-form-label search-label">' . $label . '</span>';
                    if (!in_array($this->option['ftype'][$field], ['select', 'select2', 'radio', 'checkbox'])) {
                        $searchPer .= PHP_EOL . '            <input type="text" class="form-control search-input" name="' . $field . '" value="<?= $params[\'' . $field . '\'] ?>">';
                    } else {
                        $isSelect2 = $this->option['ftype'][$field] == 'select2' ? ' select2' : '';
                        $searchPer .= PHP_EOL . '            <select class="form-control search-input' . $isSelect2 . '" name="' . $field . '">';
                        $searchPer .= PHP_EOL . '                <option value="">请选择</option>';
                        $searchPer .= PHP_EOL . '                <?php foreach ($' . $res['variable'] . ' as $k => $v): ?>';
                        $searchPer .= PHP_EOL . '                <option value="<?= $k ?>" <?= $params[\'' . $field . '\'] == (string)$k ? \'selected\' : \'\' ?>><?= $v ?></option>';
                        $searchPer .= PHP_EOL . '                <?php endforeach; ?>';
                        $searchPer .= PHP_EOL . '            </select>';
                    }
                    $searchPer .= PHP_EOL . '        </div>';
                }
            }
            if (isset($this->option['fpagesearch2'][$field]) && $this->option['fpagesearch2'][$field] == 1) {
                $searchs[] = '\'' . $field . '\' => \'' . $label . '\'';
            }
        }
        $searchList = '[' . implode(', ', $searchs) . ']';
        $statusIndex = '';
        if (isset($this->option['fcomment']['status']) && $this->option['fchoice']['status'] == 1 && count(($statusList = $this->getChooseList('status')['list'])) == 2) {
            $statusIndex = PHP_EOL . '                    <?php if ($v[\'status\'] == 1): ?>
                    <div class="btn btn-danger btn-sm set-status-btn" data-id="<?= $v[\'' . $this->primaryKey . '\'] ?>"
                         data-url="<?= \App\Helper\UrlHelper::instance()->to(\'' . lcfirst($this->module) . '/' . $this->controllerUrl . '/set-status\') ?>"
                         data-status="0">
                        <i class="glyphicon glyphicon-remove-circle"></i> <span>禁用</span>
                    </div>
                    <?php else: ?>
                    <div class="btn btn-success btn-sm set-status-btn" data-id="<?= $v[\'' . $this->primaryKey . '\'] ?>"
                         data-url="<?= \App\Helper\UrlHelper::instance()->to(\'' . lcfirst($this->module) . '/' . $this->controllerUrl . '/set-status\') ?>"
                         data-status="1">
                        <i class="glyphicon glyphicon-ok-circle"></i> <span>启用</span>
                    </div>
                    <?php endif; ?>';
        }
        $sortIndex = '';
        if (isset($this->option['fcomment']['sort'])) {
            $sortIndex = PHP_EOL . '                    <div class="btn btn-info btn-sm set-sort-btn" data-id="<?= $v[\'' . $this->primaryKey . '\'] ?>"
                         data-url="<?= \App\Helper\UrlHelper::instance()->to(\'' . lcfirst($this->module) . '/' . $this->controllerUrl . '/set-sort\') ?>">
                        <i class="glyphicon glyphicon-sort"></i> 设置排序
                    </div>';
        }
        $filename = $dir . '/index.blade.php';
        $file = $this->templatePath . 'view/index';
        $str = file_get_contents($file);
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{controllerUrl}}', $this->controllerUrl, $str);
        $str = str_replace('{{mtable}}', lcfirst($this->table), $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{mmodule}}', lcfirst($this->module), $str);
        $str = str_replace('{{searchPer}}', $searchPer, $str);
        $str = str_replace('{{searchList}}', $searchList, $str);
        $str = str_replace('{{table-header}}', $header, $str);
        $str = str_replace('{{table-body}}', $body, $str);
        $str = str_replace('{{statusIndex}}', $statusIndex, $str);
        $str = str_replace('{{sortIndex}}', $sortIndex, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);

        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function getChooseList($field)
    {
        if (isset($this->option['fchoice'][$field])) {
            if ($this->option['fchoice'][$field] == 1) {
                $list = $this->option['fchoicelist'][$field] ? explode(',', $this->option['fchoicelist'][$field]) : [];
                $res = [];
                $var = $list[0];
                unset($list[0]);
                foreach ($list as $v) {
                    $arr = explode(':', $v);
                    $res[$arr[0]] = $arr[1];
                }
                return ['variable' => $var, 'list' => $res, 'type' => 1];
            } else {
                $arr = $this->option['fchoicelist'][$field] ? explode(':', $this->option['fchoicelist'][$field]) : [];
                $res = [
                    'type' => 2,
                    'variable' => $arr[3],
                    'table' => $arr[0],
                    'key' => $arr[1],
                    'value' => $arr[2],
                    'where' => $arr[2],
                ];
                return $res;
            }
        }
    }

    public function controller()
    {
        $dir = app()->path() . '/Http/Controllers/' . $this->app . '/' . $this->module;
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = $dir . '/' . $this->table . 'Controller.php';
        $file = $this->templatePath . 'controller';
        $str = file_get_contents($file);
        $otherAssign = '';
        $parseFile = '';
        $otherDefineService = '';
        $otherUse = '';
        $otherCode = '';
        foreach ($this->option['fcomment'] as $field => $label) {
            if (in_array($this->option['ftype'][$field], ['select', 'select2', 'radio', 'checkbox'])) {
                $res = $this->getChooseList($field);
                if ($res['type'] == 1) {
                    $otherDefineService .= PHP_EOL . '    public $' . $res['variable'] . ' = [';
                    foreach ($res['list'] as $key => $v) {
                        $otherDefineService .= PHP_EOL . '        \'' . $key . '\' => \'' . $v . '\',';
                    }
                    $otherDefineService .= PHP_EOL . '    ];';
                    $otherAssign .= PHP_EOL . '            \'' . $res['variable'] . '\' => $this->' . $res['variable'] . ',';
                } else {
                    $otherCode .= PHP_EOL . '        $' . $res['variable'] . ' = \Db::table(\'' . $res['table'] . '\')->field([\'' . $res['key'] . '\', \'' . $res['value'] . '\'])->findAll();';
                    $otherCode .= PHP_EOL . '        $' . $res['variable'] . ' = array_column($' . $res['variable'] . ', \'' . $res['value'] . '\',\'' . $res['key'] . '\');';
                    $otherAssign .= PHP_EOL . '            \'' . $res['variable'] . '\' => $' . $res['variable'] . ',';
                }
            } else if ($this->option['ftype'][$field] == 'image') {
                $otherUse .= PHP_EOL . 'use App\Helper\UploadHelper;';
                $parseFile .= PHP_EOL . '                $params[\'' . $field . '\'] = UploadHelper::instance()->parseFileOrUrl(\'' . $field . '\',\'' . $this->module . '/' . $this->controllerUrl . '\');';
            }
        }
        $statusAction = '';
        if (isset($this->option['fcomment']['status']) && $this->option['fchoice']['status'] == 1 && count(($statusList = $this->getChooseList('status')['list'])) == 2) {
            $statusAction = PHP_EOL . '
    /**
     * @throws \Exception
     */
    public function setStatus()
    {
        $params = $this->request;
        if ($this->request->ajax() && $this->request->isMethod(\'POST\')) {
            try {
                if (empty($params[\'id\'])) {
                    throw new \Exception(\'非法请求\');
                }
                $model = ' . $this->table . 'Model::query()->find($params[\'id\']);
                $model->status = $params[\'status\'];
                $model->save();
                return $this->success($params[\'status\'] == 1 ? \'已启用\' : \'已禁用\');
            } catch (\Exception $e) {
                return $this->error($e->getMessage());
            }
        }
    }';
        }
        $sortAction = '';
        if (isset($this->option['fcomment']['sort'])) {
            $sortAction = PHP_EOL . '
    /**
     * @throws \Exception
     */
    public function setSort()
    {
        try {
            $params = $this->request;
            if (empty($params[\'id\']) || empty($params[\'sort\'])) {
                throw new \Exception(\'非法请求\');
            }
            $model = ' . $this->table . 'Model::query()->find($params[\'id\']);
            $model->sort = $params[\'sort\'];
            $model->save();
            return $this->success(\'设置成功\');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }';
        }
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{otherDefineService}}', $otherDefineService, $str);
        $str = str_replace('{{otherUse}}', $otherUse, $str);
        $str = str_replace('{{otherAssign}}', $otherAssign, $str);
        $str = str_replace('{{otherCode}}', $otherCode, $str);
        $str = str_replace('{{parseFile}}', $parseFile, $str);
        $str = str_replace('{{controllerUrl}}', $this->controllerUrl, $str);
        $str = str_replace('{{mtable}}', lcfirst($this->table), $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{mmodule}}', lcfirst($this->module), $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);
        $str = str_replace('{{tablename}}', $this->option['name'], $str);
        $str = str_replace('{{statusAction}}', $statusAction, $str);
        $str = str_replace('{{sortAction}}', $sortAction, $str);
        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function service()
    {
        $filename = app()->path() . '/Lib/' . ucfirst($this->table) . 'Service.php';
        $file = $this->templatePath . 'service';
        $str = file_get_contents($file);
        $selectorParams = '';
        foreach ($this->option['fcomment'] as $field => $label) {
            if (isset($this->option['fpagesearch1'][$field]) && in_array($this->option['ftype'][$field], ['date', 'datetime'])) {
                $selectorParams .= PHP_EOL . '        if (isset($params[\'' . $field . '_start\']) && $params[\'' . $field . '_start\'] !== \'\') {';
                $where = '\'' . $field . '\', \'>=\', strtotime($params[\'' . $field . '_start\'])';
                $selectorParams .= PHP_EOL . '            $selector = $selector->where(' . $where . ');';
                $selectorParams .= PHP_EOL . '        }';
                $selectorParams .= PHP_EOL . '        if (isset($params[\'' . $field . '_end\']) && $params[\'' . $field . '_end\'] !== \'\') {';
                $where = '\'' . $field . '\', \'<\', strtotime($params[\'' . $field . '_end\']) + 24 * 3600';
                $selectorParams .= PHP_EOL . '            $selector = $selector->where(' . $where . ');';
                $selectorParams .= PHP_EOL . '        }';
            } else {
                $selectorParams .= PHP_EOL . '        if (isset($params[\'' . $field . '\']) && $params[\'' . $field . '\'] !== \'\') {';
                if ($this->columnTypes[$field] == 'string') {
                    $where = '\'' . $field . '\', \'like\', \'%\' . $params[\'' . $field . '\'] . \'%\'';
                } else {
                    $where = '[\'' . $field . '\' => $params[\'' . $field . '\']]';
                }
                $selectorParams .= PHP_EOL . '            $selector = $selector->where(' . $where . ');';
                $selectorParams .= PHP_EOL . '        }';
            }
        }
        $checkUnique = '';
        if (count($this->uniqueColumns)) {
            $checkUnique .= PHP_EOL . '        $selector = ' . $this->table . 'Model::query();';
            $checkUnique .= PHP_EOL . '        if (!empty($data[\'' . $this->primaryKey . '\'])) {';
            $checkUnique .= PHP_EOL . '            $selector = $selector->where(\'' . $this->primaryKey . '\', \'!=\', $data[\'' . $this->primaryKey . '\']);';
            $checkUnique .= PHP_EOL . '        }';
            $message = [];
            foreach ($this->uniqueColumns as $key => $vList) {
                $checkUnique .= PHP_EOL . '        $check = [];';
                foreach ($vList as $v) {
                    $checkUnique .= PHP_EOL . '        $check[\'' . $v . '\'] = $data[\'' . $v . '\'];';
                    $message[] = $this->option['fcomment'][$v];
                }
                $checkUnique .= PHP_EOL . '        $selector = $selector->where($check);';
            }
            $checkUnique .= PHP_EOL . '        $row = $selector->first();';
            $checkUnique .= PHP_EOL . '        if ($row) {';

            $checkUnique .= PHP_EOL . '            throw new \Exception(\'' . implode(',', $message) . '不能重复~\');';
            $checkUnique .= PHP_EOL . '        }';
        }

        $str = str_replace('{{checkUnique}}', $checkUnique, $str);
        $str = str_replace('{{selectorParams}}', $selectorParams, $str);
        $str = str_replace('{{table}}', $this->table, $str);
        $str = str_replace('{{controllerUrl}}', $this->table, $str);
        $str = str_replace('{{app}}', $this->app, $str);
        $str = str_replace('{{module}}', $this->module, $str);
        $str = str_replace('{{primaryKey}}', $this->primaryKey, $str);
        if (!file_exists($filename)) {
            file_put_contents($filename, $str);
        }
        return $this;
    }

    public function common()
    {
        $dir = app()->path() . '/Http/Controllers/' . $this->app . '/Common';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        $dir = app()->path() . '/Lib/' . $this->module;
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
        $filename = app()->path() . '/Lib/BaseService.php';
        if (!file_exists($filename)) {
            $file = $this->templatePath . 'base_service';
            $str = file_get_contents($file);
            $str = str_replace('{{module}}', $this->module, $str);
            file_put_contents($filename, $str);
        }
        return $this;
    }
}