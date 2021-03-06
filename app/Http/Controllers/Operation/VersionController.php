<?php
namespace App\Http\Controllers\Operation;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\MessageExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;

class VersionController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        $today = date('Y-m-d', time());
        $time   = !empty($params['dateTime']) ? explode(' - ', $params['dateTime']) : '';
        $start  = !empty($time) ? array_shift($time) : date('Y-m-d', time()-86400*7);
        $end    = !empty($time) ? array_shift($time) : $today;
        $end    = $end>$today?$today:$end;
        $month  = $end ? date('Ym', strtotime($end)) : date('Ym');
        $table  = 'visit_logs_'.$month;

        $versions   = DB::connection('lovbee')->table($table)->select(DB::raw('version, count(DISTINCT(user_id)) num, created_at date'));
        $versions   = $versions->where('version', '>', 0)->whereBetween('created_at', [$start, $end])->groupBy(DB::raw('created_at,version'))->get();

        $version = collect($versions)->pluck('version')->unique()->values()->toArray();
        $dates   = collect($versions)->pluck('date')->unique()->values()->sort()->toArray();
        $forList = array();

        foreach ($dates as $date) {
            $total  = 0;
            foreach ($version as $item) {
                $num = collect(collect($versions)->where('date', $date)->where('version', $item)->first())->get('num' , 0);
                $forList[$item][] = $num;
                $total = !empty($total) ? $total + $num : $num;
            }
            $count[] = $total ?? 0;
        }
        foreach ($forList as $key=>$value) {
            $line[] = [
                "name" => $key,
                "type" => "line",
                "data" => $value,
                'markPoint' => ['data' =>[['type'=>'max', 'name'=>'MAX'], ['type'=>'min', 'name'=>'MIN']]],
                'itemStyle' => ['normal'=>['label'=>['show'=>true]]]
            ];
        }
        $line[] = [
            "name" => 'Total',
            "type" => "line",
            "data" => $count ?? [],
            'markPoint' => ['data' =>[['type'=>'max', 'name'=>'MAX'], ['type'=>'min', 'name'=>'MIN']]],
            'markLine'  => ['data' =>[['type'=>'average']]],
            'itemStyle' => ['normal'=>['label'=>['show'=>true]]]
        ];

        $params['dateTime'] = $start. ' - '.$end;
        return view('backstage.operation.version.index', compact('params','versions', 'version', 'dates','line'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * ??????
     */
    public function export(Request $request)
    {
        ini_set('memory_limit','256M');

        $now       = Carbon::now();
        $startDate = $now->startOfDay()->toDateTimeString();
        $endDate   = $now->endOfDay()->toDateTimeString();
        $params    = $request->all();
        $date      = $request->input('dateTime' , $startDate.' - '.$endDate);
        $allDate   = explode(' - ' , $date);
        $start     = array_shift($allDate);
        $end       = array_pop($allDate);

        if (empty($start) || empty($end)) {
            $start = $startDate;
            $end   = $endDate;
        }
        return  Excel::download(new MessageExport($params), 'message-'.$start.'-'.$end.'.xlsx');
    }

    public function upgrade()
    {
        $app = DB::connection('lovbee')->table('app_versions')->where('status' , 1)->first();
        $version = array();
        $id = 0;
        if(!blank($app))
        {
            $version['Current'] = array('value'=>$app->version , 'field'=>'version');
            $version['Lowest'] = array('value'=>$app->last , 'field'=>'last');
            $version['Text'] = array('value'=>$app->upgrade_point , 'field'=>'upgrade_point');
            $id = $app->id;
        }
        return view('backstage.operation.version.upgrade', compact('version' , 'id'));
    }

    public function update(Request $request , $id)
    {
        $data = array();
        $version = strval($request->input('version' , ''));
        $last = strval($request->input('last' , ''));
        $upgrade_point = strval($request->input('upgrade_point' , ''));
        !blank($version)&&$data['version'] = $version;
        !blank($last)&&$data['last'] = $last;
        !blank($upgrade_point)&&$data['upgrade_point'] = $upgrade_point;
        !blank($data)&&DB::connection('lovbee')->table('app_versions')->where('id' , $id)->update($data);
        $this->httpRequest('api/backstage/version/upgrade' , [] , "PATCH");
        return response()->json([
            'result' => 'success',
        ]);
    }

}