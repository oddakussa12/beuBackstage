<?php
namespace App\Http\Controllers\Operator;

use App\Exports\MessageExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class VersionController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        $time   = !empty($params['dateTime']) ? explode(' - ', $params['dateTime']) : '';
        $start  = !empty($time) ? array_shift($time) : date('Y-m-d', time()-86400*7);
        $end    = !empty($time) ? array_shift($time) : date('Y-m-d', time());
        $month  = $end ? date('Ym', strtotime($end)) : date('Ym');
        $table  = 'visit_logs_'.$month;

        $list   = DB::connection('lovbee')->table($table)->select(DB::raw('version, count(DISTINCT(user_id)) num, created_at date'));
        $list   = $list->where('version', '>', 0)->whereBetween('created_at', [$start, $end])->groupBy(DB::raw('created_at,version'))->orderByDesc('created_at')->get();

        $version= collect($list)->pluck('version')->unique()->values()->toArray();
        $dates  = collect($list)->pluck('date')->unique()->values()->sort()->toArray();

        foreach ($dates as $date) {
            $total  = 0;
            foreach ($version as $item) {
                $num = collect($list)->where('date', $date)->where('version', $item)->pluck('num')->toArray();
                $num = !empty($num) ? current($num) : 0;
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
        return view('backstage.operator.version.index', compact('params','list', 'version', 'dates','line'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * 导出
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

}