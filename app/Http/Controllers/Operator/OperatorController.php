<?php
namespace App\Http\Controllers\Operator;

use App\Exports\MessageExport;
use App\Models\Passport\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class OperatorController extends Controller
{
    private $table;

    public function __construct() {
        $this->table = 'network_logs';
    }
    public function network(Request $request)
    {
        $table  = $this->table;
        $params = $request->all();
        $params['dateTime'] = $params['dateTime'] ?? date('Y-m-d', strtotime('-7day')).' - '.date('Y-m-d');
        $time   = explode(' - ', $params['dateTime']);
        $start  = current($time);
        $end    = last($time);
        $list   = DB::connection('lovbee')->table($table)->select(DB::raw("t_$table.*"), 'users.user_name','users.user_nick_name');
        $list   = $list->leftJoin('users', 'users.user_id', '=', "$table.user_id")->whereBetween('time', [$start, $end]);
        $fields = ['app_version', 'system_version', 'networking', 'network_type'];

        foreach ($fields as $field) {
            if (!empty($params[$field])) {
                $list = $list->where("$table.$field", $params[$field]);
            }
        }

        if (!empty($params['keyword'])) {
            $keyword = trim($params['keyword']);
            $list    = $list->where(function($query)use($keyword){$query->where('user_name', 'like', "%{$keyword}%")->orWhere('user_nick_name', 'like', "%{$keyword}%");});
        }
        $list = $list->orderByDesc('time')->paginate(10);
        $params['appends']  = $params;
        $params['list']     = $list;
        return view('backstage.operator.network', $params);
    }

    public function feedback(Request $request)
    {
        $params = $request->all();
        $params['dateTime'] = $params['dateTime'] ?? date('Y-m-d', strtotime('-7day')).' - '.date('Y-m-d');

        $table  = 'feedback';
        $result = DB::connection('lovbee')->table($table)->select(DB::raw("t_$table.*"), 'users.user_name','users.user_nick_name');
        $result = $result->leftJoin('users', 'users.user_id', '=', "$table.user_id");

        if (isset($params['status'])) {
            if (in_array($params['status'], [0, 1, '0', '1'])) {
                $result = $result->where("$table.status", $params['status']);
            }
        } else {
            $result = $result->where("$table.status", 0);
        }
        if (!empty($params['keyword'])) {
            if (!empty($params['keyword'])) {
                $keyword = trim($params['keyword']);
                $result  = $result->where(function($query)use($keyword){$query->where('user_name', 'like', "%{$keyword}%")->orWhere('user_nick_name', 'like', "%{$keyword}%");});
            }
        }
        $result = $result->orderByDesc('id')->paginate(10);
        foreach ($result as $item) {
            $item->image = !empty($item->image) ? explode(';', $item->image) : [];
        }

        $params['list'] = $result;
        return view('backstage.operator.feedback', $params);
    }
}