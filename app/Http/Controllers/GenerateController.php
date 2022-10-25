<?php
/**
 * User: thomas
 * Date: 2021/6/8
 * Email: <thomas.wang@heavengifts.com>
 */

namespace App\Http\Controllers;

use App\Helper\Generate\Generate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller as BaseController;

class GenerateController extends BaseController
{
    /** @var Request */
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index()
    {
        if ($this->request->isMethod('POST')) {
            $data = $this->request->request->all();
            if ($data['type'] == 'show-table') {
                try {
                    $prefix = app()->config['database.connections.mysql.prefix'];
                    $table = strtolower($data['table']);
                    $sql = 'show columns from ' . $prefix . $table . ';';
                    $list = DB::select($sql);
                    $list = json_decode(json_encode($list), true);
                    $fields= array_column($list, null, 'Field');
                    echo json_encode(['code' => 200, 'data' => array_keys($fields)]);
                } catch (\Exception $e) {
                    echo json_encode(['code' => 400, 'message' => $e->getMessage()]);
                }
                die;
            } else {
                if (isset($data['fcomment'])) {
                  Generate::instance($data)->run();
                }
            }
        }
        return view('generate');
    }
}