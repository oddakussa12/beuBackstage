<?php
namespace App\Http\Controllers\Operation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class NetworkLogController extends Controller
{

    public function index(Request $request)
    {
        $table  = 'network_logs';
        $params = $request->all();
        $logs   = DB::connection('lovbee')->table($table)->select(DB::raw("t_network_logs.*"), 'users.user_name','users.user_nick_name')->leftJoin('users', 'users.user_id', '=', "$table.user_id");
        if(isset($params['dateTime']))
        {
            $dateTime = $this->parseTime($params['dateTime']);
            $dateTime!==false&&$logs = $logs->whereBetween('time', [$dateTime['start'], $dateTime['end']]);
        }
        $fields = ['app_version', 'system_version', 'networking', 'network_type'];
        foreach ($fields as $field) {
            if (!empty($params[$field])) {
                $logs = $logs->where("$table.$field", $params[$field]);
            }
        }
        if (!empty($params['keyword'])) {
            $keyword = trim($params['keyword']);
            !empty($keyword)&&$logs = $logs->where(function($query)use($keyword){$query->where('user_name', 'like', "%$keyword%")->orWhere('user_nick_name', 'like', "%{$keyword}%");});
        }
        $logs = $logs->orderByDesc('time')->paginate(10)->appends($params);
        $params['appends']  = $params;
        $params['logs']     = $logs;
        $params['appVersions']    = $logs->pluck('app_version')->unique()->toArray();
        $params['systemVersions'] = $logs->pluck('system_version')->unique()->toArray();
        $params['networks']       = $logs->pluck('networking')->unique()->toArray();
        $params['networkTypes']   = $logs->pluck('network_type')->unique()->toArray();
        return view('backstage.operation.network_log.index', $params);
    }

}