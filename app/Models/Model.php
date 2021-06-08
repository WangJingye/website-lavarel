<?php
/**
 *
 * @author      jason
 * @copyright   (c) dms_api , Inc
 * @project     dms_api
 * @since       2021/3/26 10:42 AM
 * @version     1.0.0
 *
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;

/**
 *
 * @return Builder Model
 */
class Model extends EloquentModel
{
    protected $connection = "mysql";
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';
    protected $casts = [
        'create_time' => 'int',
        'update_time' => 'int'
    ];

    public function freshTimestamp()
    {
        return time();
    }

    public function fromDateTime($value)
    {
        return $value;
    }

    public function __construct(array $attributes = [])
    {
        \Illuminate\Database\Query\Builder::macro('findInSet', function ($field, $value) {
            return $this->whereRaw("FIND_IN_SET(?, {$field})", $value);
        });

        parent::__construct($attributes);
    }

    /**
     * 加载数据到model中
     * @param $data
     */
    public function loadData($data)
    {
        //主键不存在则去除data中的主键key
        if (isset($data[$this->primaryKey]) && !$data[$this->primaryKey]) {
            unset($data[$this->primaryKey]);
        }
        //不存在数据库的字段不赋值
        $columnList = Schema::getColumnListing($this->table);
        foreach ($data as $key => $v) {
            if (!in_array($key, $columnList)) {
                continue;
            }
            $this->$key = $v;
        }
        return $this;
    }
}
