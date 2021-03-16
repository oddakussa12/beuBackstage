<?php

namespace App\Http\Controllers\Passport;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Models\Passport\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class FriendController extends Controller
{
    /**
     * @var UserRepository
     */
    private $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $params['appends'] = $params;
        $counties = config('country');
        $params['counties']=$counties;
        $users = $this->user->friendManage($params);
        $block_users = block_user_list();
        $users->each(function ($item) use ($block_users){
            $item->is_block = intval(in_array($item->user_id , array_keys($block_users)));
            $item->user_format_created_at = Carbon::parse($item->user_created_at)->addHours(8)->toDateTimeString();
        });

        $params['users']  = $users;

        $time   = !empty($params['dateTime']) ? explode(' - ', $params['dateTime']) : '';
        $start  = !empty($time) ? strtotime(array_shift($time)) : time()-86400*31;
        $end    = !empty($time) ? strtotime(array_shift($time)) : time();
        $list   = DB::connection('lovbee')->table('users_friends')->select(DB::raw("count(DISTINCT(t_users_friends.user_id)) num, FROM_UNIXTIME(t_users_friends.created_at, '%Y-%m-%d') date"));
        $list   = $list->join('users_countries', 'users_countries.user_id', '=', 'users_friends.user_id');
        if (!empty($params['num'])) {
            $list = $list->having(DB::raw("count(DISTINCT(t_users_friends.user_id))"), '>=', (int)$params['num']);
        }
        if (!empty($params['country_code'])) {
            $list = $list->where('users_countries.country', strtolower($params['country_code']));
        }
        $list   = $list->whereBetween('users_friends.created_at', [$start, $end])->groupBy(DB::raw("FROM_UNIXTIME(t_users_friends.created_at, '%Y-%m-%d')"))->get()->toArray();
        $dates  = $this->printDates($start,$end);

        foreach ($dates as $date) {
            $res = collect($list)->where('date', $date)->pluck('num')->toArray();
            $num[] = !empty($res) ? current($res) : 0;
        }
        $params['dates']  = $dates;
        $params['line'][] = [
            "name" => 'Friend Count',
            "type" => "line",
            "data" => $num ?? [],
        ];
        return view('backstage.passport.friend.index', $params);
    }

    public function printDates($start,$end): array
    {
        $dt_start = strpos($start, '-')!==false ? strtotime($start) : $start;
        $dt_end   = strpos($end, '-')!==false   ? strtotime($end)   : $end;
        while ($dt_start<=$dt_end){
             $date[]  = date('Y-m-d',$dt_start);
            $dt_start = strtotime('+1 day',$dt_start);
        }
        return $date ?? [];
    }

    public function msgExport(Request $request)
    {
        ini_set('memory_limit','256M');
        $now = Carbon::now();
        $startDate = $now->startOfDay()->toDateTimeString();
        $endDate = $now->endOfDay()->toDateTimeString();
        $params = $request->all();
        $date = $request->input('dateTime' , $startDate.' - '.$endDate);
        $allDate = explode(' - ' , $date);
        $start = array_shift($allDate);
        $end = array_pop($allDate);
        if(empty($start)||empty($end))
        {
            $start = $startDate;
            $end = $endDate;
        }
        return  Excel::download(new UsersExport($params), 'user-'.$start.'-'.$end.'.xlsx');
    }

    public function export(Request $request)
    {
        ini_set('memory_limit','256M');
        $now = Carbon::now();
        $startDate = $now->startOfDay()->toDateTimeString();
        $endDate = $now->endOfDay()->toDateTimeString();
        $params = $request->all();
        $date = $request->input('dateTime' , $startDate.' - '.$endDate);
        $allDate = explode(' - ' , $date);
        $start = array_shift($allDate);
        $end = array_pop($allDate);
        if(empty($start)||empty($end))
        {
            $start = $startDate;
            $end = $endDate;
        }
        return  Excel::download(new UsersExport($params), 'user-'.$start.'-'.$end.'.xlsx');
    }

    public function actionStatistics(Request $request)
    {
        $defaultDate = date('Y-m-d' , time());
        $defaultDiffDate = $defaultDate.' - '.$defaultDate;
        $duration = '0秒';
        $dailyNumber = $newUser = $startTurn = $yt = array();
        $app = array(
            array(
                'newUsers'=>0,
                'totalUsers'=>0,
                'activityUsers'=>0,
                'launches'=>0
            ),
            array(
                'newUsers'=>0,
                'totalUsers'=>0,
                'activityUsers'=>0,
                'launches'=>0,
                'duration'=>'0秒'
            )
        );
        $type = $request->input('type');
        if($type=='duration'||$type=='app')
        {
            $dateTime = $request->input('dateTime' , date('Y-m-d' , time()));
            if($dateTime>date('Y-m-d' , time()))
            {
                $dateTime = date('Y-m-d' , time());
            }

            if($type=='duration')
            {
//                $duration = \json_decode($this->duration($dateTime) , true);
//                $time = $duration['result'];
//                $d = floor($time / (3600*24));
//                $h = floor(($time % (3600*24)) / 3600);
//                $m = floor((($time % (3600*24)) % 3600) / 60);
//                $s = $time-$d*86400-$h*3600-$m*60;
//                if($d>'0'){
//                    $duration = $d.'天'.$h.'小时'.$m.'分'.$s.'秒';
//                }else{
//                    if($h!='0'){
//                        $duration = $h.'小时'.$m.'分'.$s.'秒';
//                    }else{
//                        $duration = $m.'分'.$s.'秒';
//                    }
//                }
            }else{
                $ios = \json_decode($this->app($dateTime , 0) , true);
                $android = \json_decode($this->app($dateTime , 1) , true);

                $app[0] = $ios['result'];
                $app[1] = $android['result'];


                $duration = \json_decode($this->duration($dateTime) , true);
                $time = $duration['result'];
                $d = floor($time / (3600*24));
                $h = floor(($time % (3600*24)) / 3600);
                $m = floor((($time % (3600*24)) % 3600) / 60);
                $s = $time-$d*86400-$h*3600-$m*60;
                if($d>'0'){
                    $duration = $d.'天'.$h.'小时'.$m.'分'.$s.'秒';
                }else{
                    if($h!='0'){
                        $duration = $h.'小时'.$m.'分'.$s.'秒';
                    }else{
                        $duration = $m.'分'.$s.'秒';
                    }
                }
                $app['1']['duration'] = $duration;
            }
        }elseif ($type=='dailyNumber'||$type=='newUser'||$type=='startTurn')
        {
            $dateTime = $request->input('dateTime' , date('Y-m-d' , time()).' - '.date('Y-m-d' , time()));
            $allDate = explode(' - ' , $dateTime);
            $startDate = array_shift($allDate);
            $endDate = array_pop($allDate);
            if($endDate>date('Y-m-d' , time()))
            {
                $endDate = date('Y-m-d' , time());
            }
            if($startDate>date('Y-m-d' , time()))
            {
                $startDate = date('Y-m-d' , time());
            }

            if($startDate>$endDate)
            {
                $startDate = $endDate;
            }


            if($type=='dailyNumber')
            {
                $dailyNumber = \json_decode($this->dailyNumber($startDate , $endDate) , true);
                foreach ($dailyNumber as $k => $v)
                {
                    $dailyNumber[$k]['value'] = $v['value']+200;
                }
            }elseif ($type=='newUser')
            {
                $newUser[0] = \json_decode($this->newUser($startDate , $endDate , 0) , true);
                $newUser[1] = \json_decode($this->newUser($startDate , $endDate) , true);

            }else{
                $startTurn = \json_decode($this->startTurn($startDate , $endDate) , true);
            }

        }else{
            $android = \json_decode($this->yt() , true);
            $ios = \json_decode($this->yt(0) , true);
            $yt[0] = $ios['result'];
            $yt[1] = $android['result'];
        }

        return view('backstage.passport.user.statistics' , compact('type' , 'dateTime' , 'defaultDate' , 'defaultDiffDate' , 'dailyNumber' , 'duration' , 'yt' , 'newUser' , 'startTurn' , 'app'));
    }

    public function app($date, $type=1)
    {
        $url = "http://test.api.mmantou.cn/Haca9cb27e73e4ea2b94de94375144a6dM/app.php?type={$type}&date={$date}";
        return file_get_contents($url);
    }

    public function startTurn($startDate , $endDate)
    {
        $url = "http://test.api.mmantou.cn/Haca9cb27e73e4ea2b94de94375144a6dM/startTurn.php?type=1&startDate={$startDate}&endDate={$endDate}";
        return file_get_contents($url);
    }

    public function newUser($startDate , $endDate , $type=1)
    {
        $url = "http://test.api.mmantou.cn/Haca9cb27e73e4ea2b94de94375144a6dM/newUser.php?type={$type}&startDate={$startDate}&endDate={$endDate}";
        return file_get_contents($url);
    }

    protected function yt($type=1)
    {
        $url = "http://test.api.mmantou.cn/Haca9cb27e73e4ea2b94de94375144a6dM/yt.php?type={$type}";
        return file_get_contents($url);
    }

    protected function dailyNumber($startDate , $endDate)
    {
        $url = "http://test.api.mmantou.cn/Haca9cb27e73e4ea2b94de94375144a6dM/dailynumber.php?type=1&startDate={$startDate}&endDate={$endDate}";
        return file_get_contents($url);
    }
    protected function duration($date)
    {
        $url = "http://test.api.mmantou.cn/Haca9cb27e73e4ea2b94de94375144a6dM/duration.php?type=1&date={$date}";
        return file_get_contents($url);
    }

    public function differenceKeep(Request $request)
    {
        set_time_limit(0);
        $addDay = $request->input('deadline' , 0);
        $dateTime = $request->input('startDate' , date('Y-m-d' , time()).' - '.date('Y-m-d' , time()));
        $date = explode(' - ' , $dateTime);
        $startTime = $date[0];
        $endTime = $date[1];
        $type = $request->input('type' , 2);
        $o = array_keys($request->input('o' , array('r'=>'on')));
        $counties = config('country');
        $country_code = $request->input('country_code' , '');
        $codes = collect($counties)->pluck('code')->all();
        $k = array_search($country_code , $codes);
        $country = $k===false?0:intval($k+1);

        $data = array();
        while ($startTime<=$endTime)
        {
            $data[$startTime] = $this->keepDifferenceLoop($addDay , $startTime , $type , $o , $country);
            $startTime = date('Y-m-d 00:00:00' , strtotime('+1 day' , strtotime($startTime)));
        }
        $userCount = collect($data)->sum('reg_count');
        $viewNum = collect($data)->sum('view_count');
        $postNum = collect($data)->sum('post_count');
        $commentNum = collect($data)->sum('comment_count');
        $likeNum = collect($data)->sum('like_count');
        $messageNum = collect($data)->sum('message_count');
        return view('backstage.passport.user.keep' , compact('addDay' ,'dateTime' , 'type' , 'o' , 'data' , 'userCount' , 'viewNum' ,'postNum' , 'commentNum' , 'likeNum' , 'messageNum' , 'country_code' , 'counties'));
    }

    public function block($id)
    {
        $user   = User::where('user_id', $id)->first();
        $client = new Client();
        $params = array(
            'user_id'   => $id,
            'time_stamp'=> time(),
            'desc'      => '后台封禁',
            'operator'  => auth()->user()->admin_username,
        );

        $params['signature'] = common_signature($params);
        $url  = front_url('api/ry/set/block');
        $data = ['form_params' => $params];
        try {
            $response = $client->request('POST', $url, $data);
            $body = $response->getBody();
            $result = \json_decode($body->getContents(),true);
            if($result['code']==200) {
                block_user($id);
            }
        } catch (GuzzleException $e) {
            abort(400 , $e->getMessage());
        }
        return response()->json([
            'result' => 'success',
        ]);
    }

    public function unblock($id)
    {
        $time = time();
        $client = new Client();
        $params = array(
            'user_id'=>$id,
            'time_stamp'=>$time,
            'operator'  => auth()->user()->admin_username,
        );
        $signature = common_signature($params);
        $params['signature'] = $signature;
        $url = front_url('api/ry/set/unblock');
        $data = [
            'form_params' => $params
        ];
        try {
            $response = $client->request('POST', $url, $data);
            $body = $response->getBody();
            $result = \json_decode($body->getContents(),true);

            if($result['code']==200)
            {
                unblock_user($id);
            }
        } catch (GuzzleException $e) {
            abort(400 , $e->getMessage());
        }
        return response()->json([
            'result' => 'success',
        ]);
    }

    public function keep(Request $request)
    {
        $v = $request->input('v' , 0);
        if($v==0)
        {
            return $this->keepV2($request);
        }
        $list = array();
        $period = $request->input('period' , '');
        $country_code = strtolower(strval($request->input('country_code' , '')));
        if(!blank($period)&&!blank($country_code))
        {
            $connection = DB::connection('lovbee');
            list($start , $end) = explode(' - ' , $period);
            do{
                $num = 0;
                $tomorrowNum = 0;
                $twoNum = 0;
                $threeNum = 0;
                $sevenNum = 0;
                $thirtyNum = 0;
                $fourteenNum = 0;
                if($country_code=='gd')
                {
                    $tz = 'America/Grenada';
                    $startTime = Carbon::createFromFormat('Y-m-d' , $start , $tz)->startOfDay()->addHours(4)->toDateTimeString();
                    $endTime = Carbon::createFromFormat('Y-m-d' , $start , $tz)->endOfDay()->addHours(4)->toDateTimeString();
                }elseif ($country_code=='tl')
                {
                    $tz = 'Asia/Dili';
                    $startTime = Carbon::createFromFormat('Y-m-d' , $start , $tz)->startOfDay()->subHours(9)->toDateTimeString();
                    $endTime = Carbon::createFromFormat('Y-m-d' , $start , $tz)->endOfDay()->subHours(9)->toDateTimeString();
                }elseif ($country_code=='mu')
                {
                    $tz = 'Indian/Mauritius';
                    $startTime = Carbon::createFromFormat('Y-m-d', $start, $tz)->startOfDay()->subHours(4)->toDateTimeString();
                    $endTime = Carbon::createFromFormat('Y-m-d', $start, $tz)->endOfDay()->subHours(4)->toDateTimeString();
                }else{
                    $tz = null;
                    $startTime = Carbon::createFromFormat('Y-m-d' , $start)->startOfDay()->subHours(8)->toDateTimeString();
                    $endTime = Carbon::createFromFormat('Y-m-d' , $start)->endOfDay()->subHours(8)->toDateTimeString();
                }
                Log::info('$start' , array($start));
                Log::info('$startTime' , array($startTime));
                Log::info('$endTime' , array($endTime));
                $connection->table('users_countries')
                    ->where('activation' , 1)
                    ->where('country' , $country_code)
                    ->where('created_at' , '>=' , $startTime)
                    ->where('created_at' , '<=' , $endTime)
                    ->orderByDesc('user_id')->chunk(200 , function ($users) use ($connection , $start , $tz , &$num , &$tomorrowNum , &$twoNum , &$threeNum , &$sevenNum , &$fourteenNum , &$thirtyNum){
                        $num = $num+count($users);
                        $userIds = $users->pluck('user_id')->all();
                        $tomorrow = Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(1);
                        $s = $tomorrow->startOfDay()->timestamp;
                        $e = $tomorrow->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s , 'Asia/Shanghai')->format('Ym');
                        $nm = Carbon::createFromTimestamp($e , 'Asia/Shanghai')->format('Ym');
                        if($pm==$nm)
                        {
                            $tomorrowTable = 'visit_logs_'.$pm;
                            $tomorrowT = $connection->table($tomorrowTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $tomorrow->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $tomorrow->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $tomorrowPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $s)->count(DB::raw('DISTINCT(user_id)'));
                            $tomorrowNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $e)->count(DB::raw('DISTINCT(user_id)'));
                            $tomorrowT = $tomorrowPT+$tomorrowNT;
                        }
                        $tomorrowNum = $tomorrowNum+$tomorrowT;



                        $twoDays = Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(2);
                        $s = $twoDays->startOfDay()->timestamp;
                        $e = $twoDays->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s , 'Asia/Shanghai')->format('Ym');
                        $nm = Carbon::createFromTimestamp($e , 'Asia/Shanghai')->format('Ym');
                        if($pm==$nm)
                        {
                            $twoDaysTable = 'visit_logs_'.$pm;
                            $twoDaysT = $connection->table($twoDaysTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $twoDays->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $twoDays->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $twoDaysPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $s)->count(DB::raw('DISTINCT(user_id)'));
                            $twoDaysNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $e)->count(DB::raw('DISTINCT(user_id)'));
                            $twoDaysT = $twoDaysPT+$twoDaysNT;
                        }
                        $twoNum = $twoNum+$twoDaysT;



                        $threeDays= Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(3);
                        $s = $threeDays->startOfDay()->timestamp;
                        $e = $threeDays->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s , 'Asia/Shanghai')->format('Ym');
                        $nm = Carbon::createFromTimestamp($e , 'Asia/Shanghai')->format('Ym');
                        if($pm==$nm)
                        {
                            $threeDaysTable = 'visit_logs_'.$pm;
                            $threeDaysT = $connection->table($threeDaysTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $threeDays->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $threeDays->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $threeDaysPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $s)->count(DB::raw('DISTINCT(user_id)'));
                            $threeDaysNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $e)->count(DB::raw('DISTINCT(user_id)'));
                            $threeDaysT = $threeDaysPT+$threeDaysNT;
                        }
                        $threeNum = $threeNum+$threeDaysT;



                        $sevenDays= Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(7);
                        $s = $sevenDays->startOfDay()->timestamp;
                        $e = $sevenDays->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s , 'Asia/Shanghai')->format('Ym');
                        $nm = Carbon::createFromTimestamp($e , 'Asia/Shanghai')->format('Ym');
                        if($pm==$nm)
                        {
                            $sevenDaysTable = 'visit_logs_'.$pm;
                            $sevenDaysT = $connection->table($sevenDaysTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $sevenDays->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $sevenDays->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $sevenDaysPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $s)->count(DB::raw('DISTINCT(user_id)'));
                            $sevenDaysNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $e)->count(DB::raw('DISTINCT(user_id)'));
                            $sevenDaysT = $sevenDaysPT+$sevenDaysNT;
                        }
                        $sevenNum = $sevenNum+$sevenDaysT;

                        $fourteenDays= Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(14);
                        $s = $fourteenDays->startOfDay()->timestamp;
                        $e = $fourteenDays->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s , 'Asia/Shanghai')->format('Ym');
                        $nm = Carbon::createFromTimestamp($e , 'Asia/Shanghai')->format('Ym');
                        if($pm==$nm)
                        {
                            $fourteenDaysTable = 'visit_logs_'.$pm;
                            $fourteenDaysT = $connection->table($fourteenDaysTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $fourteenDays->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $fourteenDays->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $fourteenDaysPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $s)->count(DB::raw('DISTINCT(user_id)'));
                            $fourteenDaysNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $e)->count(DB::raw('DISTINCT(user_id)'));
                            $fourteenDaysT = $fourteenDaysPT+$fourteenDaysNT;
                        }
                        $fourteenNum = $fourteenNum+$fourteenDaysT;



                        $thirtyDays= Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(30);
                        $s = $thirtyDays->startOfDay()->timestamp;
                        $e = $thirtyDays->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s , 'Asia/Shanghai')->format('Ym');
                        $nm = Carbon::createFromTimestamp($e , 'Asia/Shanghai')->format('Ym');
                        if($pm==$nm)
                        {
                            $thirtyDaysTable = 'visit_logs_'.$pm;
                            $thirtyDaysT = $connection->table($thirtyDaysTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $thirtyDays->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $thirtyDays->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $thirtyDaysPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $s)->count(DB::raw('DISTINCT(user_id)'));
                            $thirtyDaysNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $e)->count(DB::raw('DISTINCT(user_id)'));
                            $thirtyDaysT = $thirtyDaysPT+$thirtyDaysNT;
                        }
                        $thirtyNum = $thirtyNum+$thirtyDaysT;


                    });
                $list[$start] = array(
                    'num'=>$num,
                    'tomorrowNum'=>$tomorrowNum,
                    'twoNum'=>$twoNum,
                    'threeNum'=>$threeNum,
                    'sevenNum'=>$sevenNum,
                    'fourteenNum'=>$fourteenNum,
                    'thirtyNum'=>$thirtyNum,
                );
//                Log::info('test' , array('$start:'.$start.' $num:'.$num.' $tomorrowNum:'.$tomorrowNum.' $twoNum:'.$twoNum.' $threeNum:'.$threeNum.' $sevenNum:'.$sevenNum.' $thirtyNum:'.$thirtyNum));
                $start = Carbon::createFromFormat('Y-m-d' , $start)->addDays(1)->toDateString();
            }while ($start != $end);
            Log::info('list' , $list);
            foreach($list as $d=>$l)
            {
                $result = $connection->table('data_retentions')->where('country' , $country_code)->where('date' , $d)->first();
                if(blank($result))
                {
                    $connection->table('data_retentions')->insert(
                        array(
                            'date'=>$d,
                            'country'=>$country_code,
                            'new'=>$l['num'],
                            '1'=>$l['tomorrowNum'],
                            '2'=>$l['twoNum'],
                            '3'=>$l['threeNum'],
                            '7'=>$l['sevenNum'],
                            '14'=>$l['fourteenNum'],
                            '30'=>$l['thirtyNum'],
                            'created_at'=>Carbon::now(new \DateTimeZone('UTC'))->toDateTimeString(),
                        )
                    );
                }else{
                    $connection->table('data_retentions')->where('id' , $result->id)->update(array(
                        'new'=>$l['num'],
                        '1'=>$l['tomorrowNum'],
                        '2'=>$l['twoNum'],
                        '3'=>$l['threeNum'],
                        '7'=>$l['sevenNum'],
                        '14'=>$l['fourteenNum'],
                        '30'=>$l['thirtyNum'],
                    ));
                }
            }
        }

        $counties = config('country');
        return  view('backstage.passport.user.keep' , compact('period' , 'counties' , 'country_code' , 'list' , 'v'));
    }

    public function keepV2(Request $request)
    {
        $list = array();
        $v = $request->input('v' , 0);
        $period = $request->input('period' , '');
        $country_code = strtolower(strval($request->input('country_code' , '')));
        $dates = array();
        if(!blank($period))
        {
            $connection = DB::connection('lovbee');
            list($start , $end) = explode(' - ' , $period);
            do{
                array_push($dates , $start);
                $start = Carbon::createFromFormat('Y-m-d' , $start)->addDays(1)->toDateString();
            }while($start <= $end);
            if(blank($country_code)||$country_code=='all')
            {
                $list = $connection->table('data_retentions')
                    ->whereIn('date' , $dates)->groupBy('date')->orderBy('date')
                    ->select('date' , DB::raw("sum(`new`) as `num`") ,DB::raw("sum(`1`) as `tomorrowNum`") , DB::raw("sum(`2`) as `twoNum`") , DB::raw("sum(`3`) as `threeNum`") , DB::raw("sum(`7`) as `sevenNum`") , DB::raw("sum(`14`) as `fourteenNum`") , DB::raw("sum(`30`) as `thirtyNum`"))
                    ->get()->map(function ($value) {
                        return (array)$value;
                    })->keyBy('date')->toArray();
            }else{
                $list = $connection->table('data_retentions')
                    ->where('country' , $country_code)
                    ->whereIn('date' , $dates)->orderBy('date')
                    ->select('date' , 'new as num' , '1 as tomorrowNum' , '2 as twoNum' , '3 as threeNum' , '7 as sevenNum' , '14 as fourteenNum' , '30 as thirtyNum')
                    ->get()->map(function ($value) {
                        return (array)$value;
                    })->keyBy('date')->toArray();
            }

            foreach ($dates as $date)
            {
                if(!isset($list[$date]))
                {
                    $list[$date] = array(
                        'num'=>0,
                        'tomorrowNum'=>0,
                        'twoNum'=>0,
                        'threeNum'=>0,
                        'sevenNum'=>0,
                        'fourteenNum'=>0,
                        'thirtyNum'=>0,
                    );
                }
            }
            /**
             *
            'num'=>$num,
            'tomorrowNum'=>$tomorrowNum,
            'twoNum'=>$twoNum,
            'threeNum'=>$threeNum,
            'sevenNum'=>$sevenNum,
            'fourteenNum'=>$fourteenNum,
            'thirtyNum'=>$thirtyNum,
             */
        }
        $counties = config('country');
        return  view('backstage.passport.user.keep' , compact('period' , 'counties' , 'country_code' , 'list' , 'v'));
    }

    public function allDau(Request $request)
    {
        $period = $request->input('period' , '');
        $dates = array();
        if(!blank($period))
        {
            list($start , $end) = explode(' - ' , $period);
            do{
                array_push($dates , $start);
                $start = Carbon::createFromFormat('Y-m-d' , $start)->addDays(1)->toDateString();
            }while($start <= $end);
        }
        $list = DB::connection('lovbee')->table('dau_counts')->whereIn('date' , $dates)->select('date' , 'dau' , '0 as zero' , '1 as one' , '2 as two' , 'gt3')->get();
        $list = collect($list->map(function ($value) {return (array)$value;})->toArray())->keyBy('date')->toArray();
        foreach ($dates as $d)
        {
            if(!isset($list[$d]))
            {
                $list[$d] = array(
                    'date'=>$d,
                    'dau'=>0,
                    'zero'=>0,
                    'one'=>0,
                    'two'=>0,
                    'gt3'=>0,
                );
            }
        }

        $list = collect($list)->sortBy('date')->toArray();
        $dau = collect($list)->pluck('dau')->toJson();
        $zero = collect($list)->pluck('zero')->toJson();
        $one = collect($list)->pluck('one')->toJson();
        $two = collect($list)->pluck('two')->toJson();
        $gt3 = collect($list)->pluck('gt3')->toJson();
        $xAxis = array_keys($list);
        return  view('backstage.passport.user.dau' , compact('period' , 'list' , 'dau' , 'zero' , 'one' , 'two' , 'gt3' , 'xAxis'));
    }

    public function dau(Request $request)
    {
        $period = $request->input('period' , '');
        $country_code = strtolower(strval($request->input('country_code' , '')));
        $dates = array();
        if(!blank($period)&&!blank($country_code))
        {
            list($start , $end) = explode(' - ' , $period);
            do{
                array_push($dates , $start);
                $start = Carbon::createFromFormat('Y-m-d' , $start)->addDays(1)->toDateString();
            }while($start <= $end);
        }
        if($country_code=='all')
        {
            $list = DB::connection('lovbee')->table('dau_counts')->whereIn('date' , $dates)->select('date' , DB::raw("sum(`dau`) as `dau`") , DB::raw("sum(`0`) as `zero`") , DB::raw("sum(`1`) as `one`"), DB::raw("sum(`2`) as `two`") , DB::raw("sum(`gt3`) as `gt3`"))->groupBy('date')->get();
        }else{
            $list = DB::connection('lovbee')->table('dau_counts')->where('country' , $country_code)->whereIn('date' , $dates)->select('date' , 'dau' , '0 as zero' , '1 as one' , '2 as two' , 'gt3')->get();
        }
        $list = collect($list->map(function ($value) {return (array)$value;})->toArray())->keyBy('date')->toArray();
        foreach ($dates as $d)
        {
            if(!isset($list[$d]))
            {
                $list[$d] = array(
                  'date'=>$d,
                  'dau'=>0,
                  'zero'=>0,
                  'one'=>0,
                  'two'=>0,
                  'gt3'=>0,
                );
            }
        }
        $titles = array(
            'dau',
            'zero',
            'one',
            'two',
            'gt3',
        );
        $list = collect($list)->sortBy('date')->toArray();

        $dau = collect($list)->pluck('dau');
        $zero = collect($list)->pluck('zero');
        $one = collect($list)->pluck('one');
        $two = collect($list)->pluck('two');
        $gt3 = collect($list)->pluck('gt3');

        $xAxis = array_keys($list);
        $counties = config('country');


        $utc = Carbon::tomorrow('Asia/Shanghai')->format('Y-m-d 01:00:00');

        $grenadaT = Carbon::createFromFormat("Y-m-d H:i:s" , $utc , 'America/Grenada')->timestamp;
        $diliT = Carbon::createFromFormat("Y-m-d H:i:s" , $utc , 'Asia/Dili')->timestamp;
        $mauritiusT = Carbon::createFromFormat("Y-m-d H:i:s" , $utc , 'Indian/Mauritius')->timestamp;

        $Grenada = Carbon::createFromTimestamp($grenadaT , new \DateTimeZone('UTC'))->toDateTimeString();
        $Dili = Carbon::createFromTimestamp($diliT , new \DateTimeZone('UTC'))->toDateTimeString();
        $Mauritius = Carbon::createFromTimestamp($mauritiusT , new \DateTimeZone('UTC'))->toDateTimeString();



        $GrenadaBj = Carbon::createFromTimestamp($grenadaT , 'Asia/Shanghai')->toDateTimeString();
        $DiliBj = Carbon::createFromTimestamp($diliT , 'Asia/Shanghai')->toDateTimeString();
        $MauritiusBj = Carbon::createFromTimestamp($mauritiusT , 'Asia/Shanghai')->toDateTimeString();


        return  view('backstage.passport.user.dau' , compact('utc' ,'Grenada' ,'Dili' ,'Mauritius' ,'GrenadaBj' ,'DiliBj' ,'MauritiusBj' ,'period' , 'counties' , 'country_code' , 'list' , 'dau' , 'zero' , 'one' , 'two' , 'gt3' , 'xAxis'));
    }

    public function yesterdayView(Request $request)
    {
        $jump = $request->input('jump' , 0);
        return  view('backstage.passport.user.yesterday' , compact('jump'));
    }

    public function yesterday(Request $request)
    {
        $list = array();
        $connection = DB::connection('lovbee');
        $date = $request->input('date' , Carbon::now('Asia/Shanghai')->subDays(2)->toDateString());
        $dau = $connection->table('dau_counts')->where('date' , $date)->groupBy('date')->select(DB::raw("sum(`dau`) as `dau`"))->first();
        $list['dau'] = array(
            'date'=>$date,
            'dau'=>collect($dau)->toArray(),
        );

        $new = $connection->table('data_retentions')->where('date' , $date)->groupBy('date')->select(DB::raw("sum(`new`) as `new`"))->first();
        $list['new'] = array(
            'date'=>$date,
            'new'=>collect($new)->toArray(),
        );

        $keepDate = Carbon::now('Asia/Shanghai')->subDays(2)->toDateString();
//        dump($keepDate);
        $list['keep']['date'] = $keepDate;
        $one = Carbon::createFromFormat('Y-m-d' , $keepDate , 'Asia/Shanghai')->subDays(1)->toDateString();
        $oneKeep = $connection->table('data_retentions')->where('date' , $one)->select('date' , DB::raw("sum(`new`) as `new`") , DB::raw("sum(`1`) as `one`"))->first();
        $list['keep']['one'] = array(
            'one'=>$one,
            'oneKeep'=>collect($oneKeep)->toArray(),
        );
//        dump($one);
//        dump($oneKeep);

        $two = Carbon::createFromFormat('Y-m-d' , $keepDate , 'Asia/Shanghai')->subDays(2)->toDateString();
        $twoKeep = $connection->table('data_retentions')->where('date' , $two)->select('date' , DB::raw("sum(`new`) as `new`") , DB::raw("sum(`2`) as `two`"))->first();
        $list['keep']['two'] = array(
            'two'=>$two,
            'twoKeep'=>collect($twoKeep)->toArray(),
        );
//        dump($two);
//        dump($twoKeep);

        $three = Carbon::createFromFormat('Y-m-d' , $keepDate , 'Asia/Shanghai')->subDays(3)->toDateString();
        $threeKeep = $connection->table('data_retentions')->where('date' , $three)->select('date' , DB::raw("sum(`new`) as `new`") , DB::raw("sum(`3`) as `three`"))->first();
        $list['keep']['three'] = array(
            'three'=>$three,
            'threeKeep'=>collect($threeKeep)->toArray(),
        );
//        dump($three);
//        dump($threeKeep);

        $seven = Carbon::createFromFormat('Y-m-d' , $keepDate , 'Asia/Shanghai')->subDays(7)->toDateString();
        $sevenKeep = $connection->table('data_retentions')->where('date' , $seven)->select('date' , DB::raw("sum(`new`) as `new`") , DB::raw("sum(`7`) as `seven`"))->first();
        $list['keep']['seven'] = array(
            'seven'=>$seven,
            'sevenKeep'=>collect($sevenKeep)->toArray(),
        );
//        dump($seven);
//        dump($sevenKeep);

        $fourteen = Carbon::createFromFormat('Y-m-d' , $keepDate , 'Asia/Shanghai')->subDays(14)->toDateString();
        $fourteenKeep = $connection->table('data_retentions')->where('date' , $fourteen)->select('date' , DB::raw("sum(`new`) as `new`") , DB::raw("sum(`14`) as `fourteen`"))->first();
        $list['keep']['fourteen'] = array(
            'fourteen'=>$fourteen,
            'fourteenKeep'=>collect($fourteenKeep)->toArray(),
        );
//        dump($fourteen);
//        dump($fourteenKeep);

        $thirty = Carbon::createFromFormat('Y-m-d' , $keepDate , 'Asia/Shanghai')->subDays(30)->toDateString();
        $thirtyKeep = $connection->table('data_retentions')->where('date' , $thirty)->select('date' , DB::raw("sum(`new`) as `new`") , DB::raw("sum(`30`) as `thirty`"))->first();
        $list['keep']['thirty'] = array(
            'thirty'=>$thirty,
            'thirtyKeep'=>collect($thirtyKeep)->toArray(),
        );
//        dump($thirty);
//        dump($thirtyKeep);
//        dump($list);
        return response($list);

    }

}
