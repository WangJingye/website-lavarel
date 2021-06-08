<?php

namespace App\Helper\Input;

use App\Helper\ObjectAccess;

class TimeSearch extends ObjectAccess
{
    public $name;
    public $label;
    public $params;

    public static $instance;

    public function __construct($name, $label, $params)
    {
        $this->name = $name;
        $this->label = $label;
        $this->params = $params;
    }

    public static function instance($name, $label, $params)
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($name, $label, $params);
        } else {
            self::$instance->name = $name;
            self::$instance->label = $label;
            self::$instance->params = $params;
        }
        return self::$instance;
    }

    public function show()
    {
        $html = '<span class="col-form-label search-label">' . $this->label . '</span>' .
            '<input type="date" class="form-control search-input" name="' . $this->name . '_start" placeholder="开始时间" value="' . $this->params[$this->name . '_start'] . '">' .
            '<span class="col-form-label search-label">到</span>' .
            '<input type="date" class="form-control search-input" name="' . $this->name . '_end" placeholder="结束时间" value="' . $this->params[$this->name . '_end'] . '">';
        return $html;
    }
}