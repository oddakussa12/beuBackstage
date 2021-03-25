<?php
namespace App\Http\Controllers\Operator;

use App\Exports\MessageExport;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        $list   = $list->where('version', '>', 0)->whereBetween('created_at', [$start, $end])->groupBy(DB::raw('created_at,version'))->get();

        $version = collect($list)->pluck('version')->unique()->values()->toArray();
        $dates   = collect($list)->pluck('date')->unique()->values()->sort()->toArray();
        $forList = array();

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
        $forList = array();
        foreach ($forList as $key=>$value) {
            $line[] = [
                "name" => $key,
                "type" => "line",
                "data" => $value,
                'markPoint' => ['data' =>[['type'=>'max', 'name'=>'MAX'], ['type'=>'min', 'name'=>'MIN']]],
                //'markLine'  => ['data' =>[['type'=>'average']]],
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
        return view('backstage.operator.version.upgrade', compact('version' , 'id'));
    }

    public function update(Request $request , $id)
    {
        $data = array();
        $version = strval($request->input('version' , ''));
        $last = strval($request->input('last' , ''));
        $upgrade_point = strval($request->input('upgrade_point' , ''));
//        $app = DB::connection('lovbee')->table('app_versions')->where('status' , 1)->first();
        !blank($version)&&$data['version'] = $version;
        !blank($last)&&$data['last'] = $last;
        !blank($upgrade_point)&&$data['upgrade_point'] = $upgrade_point;
        !blank($data)&&DB::connection('lovbee')->table('app_versions')->where('id' , $id)->update($data);
        $this->httpRequest('api/backStage/version/upgrade' , [] , "PATCH");
        return response()->json([
            'result' => 'success',
        ]);

    }


    /**
     * @param $url
     * @param $data
     * @param string $method
     * @param bool $json
     * @return bool
     * HTTP Request
     */
    public function httpRequest($url, $data=array(), $method='POST', $json=false)
    {
        try {
            $client = new Client();
            foreach ($data as &$datum) {
                $datum = is_array($datum) ? json_encode($datum, JSON_UNESCAPED_UNICODE) : $datum;
            }
            $signature = common_signature($data);
            $data['signature'] = $signature;
            $data     = $json ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data;
            $response = $client->request($method, front_url($url), ['form_params'=>$data]);
            $code     = intval($response->getStatusCode());
            if ($code>=300) {
                Log::info('http_request_fail' , array('code'=>$code));
                return false;
            }
            Log::info('http_request_success' , array('code'=>$code));
            return true;
        } catch (GuzzleException $e) {
            Log::info('http_request_fail' , array('code'=>$e->getCode() , 'message'=>$e->getMessage()));
            return false;
        }
    }


    public function get_color_by_scale( ) {
        $str='0123456789ABCDEF';
        $est='';
        $len=strlen($str);
        for($i=1;$i<=6;$i++) {
            $num=rand(0,$len-1);
            $est=$est.$str[$num];
        }
        if($est < 0 || hexdec($est) > hexdec('ffffff')) {
            $est = 'b4e0e1';
        }
        return  "#".$est;
    }

}