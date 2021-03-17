<?php
namespace App\Http\Controllers\Operator;

use App\Exports\MessageExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        $params['appends'] = $params;

        $time   = !empty($params['dateTime']) ? explode(' - ', $params['dateTime']) : '';
        $start  = !empty($time) ? array_shift($time) : date('Y-m-d', time()-86400*7);
        $end    = !empty($time) ? array_shift($time) : date('Y-m-d', time());
        $table  = 'chat_layers';
        $list   = DB::connection('lovbee')->table($table)->select("$table.user_id", "$table.video", "$table.num", "$table.type", "$table.country", "$table.school", "$table.time", "$table.amount", 'users.user_name','users.user_nick_name');
        $list   = $list->leftJoin('users', 'users.user_id', '=', "$table.user_id")->whereBetween('time', [$start, $end]);
        if (!empty($params['country_code'])) {
            $list = $list->where("$table.country", strtolower($params['country_code']));
        }
        if (!empty($params['school'])) {
            $list = $list->where("$table.school", strtolower($params['school']));
        }
        if (!empty($params['keyword'])) {
            $keyword = trim($params['keyword']);
            $list    = $list->where(function($query)use($keyword){$query->where('user_name', 'like', "%{$keyword}%")->orWhere('user_nick_name', 'like', "%{$keyword}%");});
        }
        $list = $list->orderByDesc('time')->orderByDesc('amount')->paginate(10);

        $params['list'] = $list;

        $chart = DB::connection('lovbee')->table($table)->select(DB::raw('count(id) num, sum(video) video, sum(amount) amount'), 'country', 'school', 'time')->whereBetween('time', [$start, $end]);
        if (!empty($params['country_code'])) {
            $chart = $chart->where('country', strtolower($params['country_code']));
        }
        if (!empty($params['school'])) {
            $chart = $chart->where('school', strtolower($params['school']));
        }

        $chart  = $chart->whereBetween('time', [$start, $end])->groupBy('time')->get()->toArray();
        $dates  = printDates($start,$end);

        foreach ($dates as $date) {
            $video = collect($chart)->where('time', $date)->pluck('video')->toArray();
            $num   = collect($chart)->where('time', $date)->pluck('num')->toArray();
            $amount= collect($chart)->where('time', $date)->pluck('amount')->toArray();
            $count['Chat'][]   = !empty($num) ? current($num) : 0;
            $count['Video'][]  = !empty($video) ? current($video) : 0;
            $count['Amount'][] = !empty($amount) ? current($amount) : 0;
        }

        $params['chart']    = $chart;
        $params['countries'] = config('country');
        $params['dateTime'] = $start.' - '.$end;
        $params['header']   = array_keys($count);
        $params['dates']    = $dates;

        foreach ($count as $key=>$value) {
            $params['line'][] = [
                "name" => $key,
                "type" => "line",
                "data" => $value,
                'areaStyle' => [],
                'markPoint' => ['data' =>[['type'=>'max', 'name'=>'MAX'], ['type'=>'min', 'name'=>'MIN']]],
                'markLine'  => ['data' =>[['type'=>'average']]],
                'itemStyle' => ['normal'=>['label'=>['show'=>true]]]
            ];
        }
        return view('backstage.operator.chat.index', $params);
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