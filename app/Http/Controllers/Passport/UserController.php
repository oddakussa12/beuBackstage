<?php

namespace App\Http\Controllers\Passport;

use App\Models\Passport\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersFriendStatusExport;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Database\Concerns\BuildsQueries;
use App\Exports\UsersFriendYesterdayStatusExport;

class UserController extends Controller
{
    use BuildsQueries;
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
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $params['appends'] = $params;
        $countries = config('country');
        $params['countries']=$countries;
        $users = $this->user->findByPaginate($params);
        $users->each(function ($item){
            $item->user_format_created_at = Carbon::parse($item->user_created_at)->addHours(8)->toDateTimeString();
        });
        $userIds = $users->pluck('user_id')->toArray();
        $block_users = DB::connection('lovbee')->table('black_users')->where('is_delete', 0)->whereIn('user_id', $userIds)->pluck('user_id')->toArray();
        $users->each(function ($item) use ($block_users){
            $item->is_block = in_array($item->user_id, $block_users);
            $item->user_format_created_at = Carbon::parse($item->user_created_at)->addHours(8)->toDateTimeString();
        });
        $params['users']=$users;
        $params = $this->userChart($params);
        return view('backstage.passport.user.index' , $params);
    }

    public function deviceInfo(Request $request)
    {
        $params = $request->all();
        $list   = DB::connection('lovbee')->table('devices')->orderByDesc('created_at')->paginate(10);
        return view('backstage.passport.user.deviceInfo' , compact('list'));
    }

    public function userChart(&$params)
    {
        $gender = DB::connection('lovbee')->table('users')->select(DB::raw('count(user_gender) num, user_gender'))->groupBy('user_gender')->get()->toArray();
        foreach ($gender as $item) {
            $sexDate[] = [
                'name' => $item->user_gender == -1 ? 'Other' : ($item->user_gender == 1 ? 'Male' : 'Female'),
                'value'=> $item->num
            ];
        }

        $country = DB::connection('lovbee')->table('users_countries')->select(DB::raw('count(country) value, country name'))->groupBy('country')->get()->toArray();
        $country = array_map(function($value) {return (array)$value;}, $country);
        $params['gender'][]  = [
            'type'   => 'pie',
            'radius' => '50%',
            'data'   => $sexDate ?? [],
            'label'  => ['normal'=>['formatter'=>"{b}: {c} {d}%"]]
        ];
        $params['chartCountry'][]  = [
            'type'   => 'pie',
            'radius' => '50%',
            'data'   => $country ?? [],
            'label'  => ['normal'=>['formatter'=>"{b}: {c} {d}%"]]
        ];
        return $params;
    }

    public function update(Request $request, $userId)
    {
        $name  = $request->input('name', '');
        $value = $request->input('value', 0);
        if ($name=='is_block' && $value) {
            return $this->block($request, $userId);
        }
        if ($name=='user_shop' && $value) {
            return $this->storeShop($request, $userId);
        }
        return [];
    }

    /**
     * @param Request $request
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     * ????????????
     */
    public function storeShop(Request $request, $userId)
    {
        $params   = $request->all();
        $userShop = $params['value'];
        if (!empty($userShop)) {
            $result = User::where('user_id', $userId)->update(['user_shop'=>1]);
            return response()->json(['result'=>$result ? 'success' : 'created failed']);
        }

        /*$data     = ['user_id'=>$userId, 'state'=>$userShop, 'operator' => auth()->user()->admin_username];
        if(!empty($userShop)) {
            $result = $this->httpRequest('api/backstage/storeShop', $data);
            if (!empty($result)) {
                return response()->json(['result' => 'success']);
            } else {
                return response()->json(['result' => 'created failed']);
            }
        }*/
        return response()->json(['result' => 'shop already existed']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * ??????
     */
    public function block(Request $request)
    {
        $params = $request->all();
        $userId = $params['user_id'];
        $block  = $params['value'];
        $data   = ['user_id'=>$userId, 'operator' => auth()->user()->admin_username];

        $time = date("Y-m-d H:i:s");

        if (!empty($block)) {
            $data['desc']       = '????????????';
            $data['operator']   = auth()->user()->admin_username;
            $data['start_time'] = $time;
            $data['end_time']   = date('Y-m-d H:i:s', time()+86400*30);
            $data['created_at'] = $time;

            $this->httpRequest('api/backstage/block/user', $data);
            return response()->json(['result' => 'success']);
        } else {
            return response()->json(['result' => 'Temporary does not support']);
            // $data['is_delete']  = 1;
            // $data['unoperator'] = auth()->user()->admin_username;
        }
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

    public function message(Request $request)
    {
        $params = $request->all();
        $params['appends'] = $params;
        $countries = config('country');
        $params['countries']=$countries;
        $users = $this->user->findMessage($params);
        $block_users = block_user_list();
        $users->each(function ($item) use ($block_users){
            $item->is_block = intval(in_array($item->user_id , array_keys($block_users)));
            $item->user_format_created_at = Carbon::parse($item->user_created_at)->addHours(8)->toDateTimeString();
        });
        $params['users']=$users;
        return view('backstage.passport.user.message' , $params);
    }

    public function chat(Request $request)
    {
        $params = $request->all();
        $params['appends'] = $params;
        if (empty($params['user_id'])) {
            $params['users'] = [];
        } else {
            $users = $this->user->findMessage($params, true);
            $params['users'] = $users;
        }
        return view('backstage.passport.user.chat', $params);
    }

    public function friend(int $userId)
    {
        $friend = DB::connection('lovbee')->table('users_friends')->select( DB::raw('t_users.*'), 'users_friends.created_at')
            ->join('users', 'users.user_id', '=', 'users_friends.friend_id')
            ->where('users_friends.user_id', $userId)->paginate(10);
        $params['users']=$friend;
        return view('backstage.passport.user.friend' , $params);
    }

    public function history($userId)
    {
        $history = DB::connection('lovbee')->table('status_logs')->where('user_id', $userId)->orderByDesc('id')->paginate(10);
        $params['users'] = $history;
        return view('backstage.passport.user.history' , $params);
    }

    public function device($id)
    {
        $month = date("Ym");
        $table = 'visit_logs_';
        $list  = [];
        for ($i=$month; $i>=202103; $i--) {
            $cTable = $table.$i;
            $result = DB::connection('lovbee')->table($cTable)->select(DB::raw('DISTINCT user_id, device_id'))->where('user_id', $id)->where('device_id', '!=', '')->get();
            $list   = array_merge($list, collect($result)->toArray());
        }
        $params['list'] = $list ;
        return view('backstage.passport.user.device' , $params);
    }

    public function deviceBlock(Request $request)
    {
        $params = $request->only('user_id', 'device_id');
        if (empty($params['user_id']) || empty($params['device_id'])) {
            return  [];
        }
        $select = DB::connection('lovbee')->table('block_devices')->where($params)->first();
        $result = $this->httpRequest('api/backstage/block/device', $params);

        /*if (!empty($select)) {
            return [];
        } else {
            $params['created_at'] = $params['updated_at'] = date('Y-m-d H:i:s');
            DB::connection('lovbee')->table('block_devices')->insert($params);
        }*/
        return [];
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

        $countries = config('country');
        return  view('backstage.passport.user.keep' , compact('period' , 'countries' , 'country_code' , 'list' , 'v'));
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
        $countries = config('country');
        return  view('backstage.passport.user.keep' , compact('period' , 'countries' , 'country_code' , 'list' , 'v'));
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
        $countries = config('country');


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


        return  view('backstage.passport.user.dau' , compact('utc' ,'Grenada' ,'Dili' ,'Mauritius' ,'GrenadaBj' ,'DiliBj' ,'MauritiusBj' ,'period' , 'countries' , 'country_code' , 'list' , 'dau' , 'zero' , 'one' , 'two' , 'gt3' , 'xAxis'));
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
        return response($list);

    }


    public function dnu(Request $request)
    {
        $type = $request->input('type' , 'country');
        $school = strval($request->input('school' , ''));
        $v = $request->input('v' , 0);
        $start = $request->input('start' , '');
        $country = strtolower(strval($request->input('country_code' , 'all')));
        if($type=='country')
        {
            if(!auth()->user()->hasRole('administrator'))
            {
                $country = $country_code = auth()->user()->admin_country;
            }
            $data =  $this->countryDnu($country , $start);
        }else{
            $data = $this->schoolDnu($school , $start);
        }
        $data['v'] = $v;
        $data['school'] = $school;
        $data['countries'] = config('country');
        $data['schools'] = config('school');
        $data['country_code'] = $country;
        $data['type'] = $type;

        return  view('backstage.passport.user.dnu' , $data);

    }

    public function countryDnu($country , $start)
    {
        $dates = array();
        if(blank($start))
        {
            $start = $startTime = Carbon::now('Asia/Shanghai')->subDays(30)->toDateString();
        }else{
            $start = $startTime = Carbon::createFromFormat('Y-m-d' , $start , 'Asia/Shanghai')->toDateString();
        }
        $end = Carbon::now('Asia/Shanghai')->subDays(2)->toDateString();
        if($country=='tl')
        {
            $tz = "Asia/Dili";
        }elseif ($country=='gd')
        {
            $tz = "America/Grenada";
        }elseif ($country=='mu')
        {
            $tz = "Indian/Mauritius";
        }elseif ($country=='id')
        {
            $tz = "Asia/Jakarta";
        }elseif ($country=='et')
        {
            $tz = "Africa/Addis_Ababa";
        }else{
            $tz = new \DateTimeZone("UTC");
        }

        $oneDaysAgo = Carbon::now('Asia/Shanghai')->subDays(1)->toDateString();
        $oneDaysStart = Carbon::createFromFormat('Y-m-d' , $oneDaysAgo , $tz)->startOfDay()->timestamp;
        $oneDaysEnd = Carbon::createFromFormat('Y-m-d' , $oneDaysAgo , $tz)->endOfDay()->timestamp;
        $oneStart = Carbon::createFromTimestamp($oneDaysStart, new \DateTimeZone("UTC"))->toDateTimeString();
        $oneEnd = Carbon::createFromTimestamp($oneDaysEnd, new \DateTimeZone("UTC"))->toDateTimeString();
        $today = Carbon::now('Asia/Shanghai')->toDateString();
        $todayStart = Carbon::createFromTimestamp(Carbon::createFromFormat('Y-m-d' , $today , $tz)->startOfDay()->timestamp , new \DateTimeZone("UTC"))->toDateTimeString();
        $todayEnd = Carbon::createFromTimestamp(Carbon::createFromFormat('Y-m-d' , $today , $tz)->endOfDay()->timestamp, new \DateTimeZone("UTC"))->toDateTimeString();
        $connection = DB::connection('lovbee');

        do{
            array_push($dates , $start);
            $start = Carbon::createFromFormat('Y-m-d' , $start)->addDays(1)->toDateString();
        }while($start <= $end);
        if($country!='all')
        {
            $list = $connection->table('data_retentions')
                ->where('country' , $country)
                ->whereIn('date' , $dates)->orderBy('date')
                ->select('date' , 'new as num')
                ->get()->map(function ($value) {
                    return (array)$value;
                })->keyBy('date')->toArray();
        }else{
            $list = $connection->table('data_retentions')
//                ->whereIn('country' , collect($countries)->pluck('code')->toArray())
                ->whereIn('date' , $dates)->orderBy('date')
                ->select('date' , DB::raw('sum(`new`) as `num`'))
                ->groupBy('date')
                ->get()->map(function ($value) {
                    return (array)$value;
                })->keyBy('date')->toArray();
        }

        foreach ($dates as $date)
        {
            if(!isset($list[$date]))
            {
                $list[$date] = array(
                    'date'=>$date,
                    'num'=>0,
                );
            }
        }
        if($country!='all')
        {
            $yesterdayCount = $connection->table('users_countries')
                ->where('country' , $country)
                ->where('activation' , 1)
                ->where('created_at' , '>=' , $oneStart)
                ->where('created_at' , '<=' , $oneEnd)
                ->count();

            $todayCount = $connection->table('users_countries')
                ->where('country' , $country)
                ->where('activation' , 1)
                ->where('created_at' , '>=' , $todayStart)
                ->where('created_at' , '<=' , $todayEnd)
                ->count();
            $list[$oneDaysAgo] = array('date'=>$oneDaysAgo , 'num'=>$yesterdayCount);
            $list[$today] = array('date'=>$today , 'num'=>$todayCount);
        }

        $list = collect($list)->sortBy('date')->toArray();

        $dnu = collect($list)->pluck('num');
        $xAxis = array_keys($list);
        return compact('startTime'   , 'list'  , 'dnu' , 'xAxis');
//        return  view('backstage.passport.user.dnu' , compact('startTime' , 'countries' , 'country_code' , 'list' , 'v' , 'dnu' , 'xAxis'));
    }

    public function schoolDnu($school , $start)
    {
        $end = Carbon::now('Asia/Shanghai')->endOfDay()->subHours(8)->toDateTimeString();
        $endTime = Carbon::now('Asia/Shanghai')->toDateTimeString();
        if(blank($start))
        {
            $startTime = $startDate = Carbon::now('Asia/Shanghai')->subDays(30)->toDateString();
            $start = Carbon::now('Asia/Shanghai')->subDays(30)->startOfDay()->subHours(8)->toDateTimeString();
        }else{
            $startTime = $startDate = Carbon::createFromFormat('Y-m-d' , $start , 'Asia/Shanghai')->toDateString();
            $start = Carbon::createFromFormat('Y-m-d' , $start , 'Asia/Shanghai')->startOfDay()->subHours(8)->toDateTimeString();
        }
        $dates = array();

        do{
            array_push($dates , $startDate);
            $startDate = Carbon::createFromFormat('Y-m-d' , $startDate)->addDays(1)->toDateString();
        }while($startDate <= $endTime);
        $connection = DB::connection('lovbee');
        $list = $connection->table('users')
            ->where('user_sl' , $school)
            ->where('user_activation' , 1)
            ->where('user_created_at' , '>=' , $start)
            ->where('user_created_at' , '<=' , $end)
            ->select(DB::raw("count(`user_id`) as `num`") , DB::raw("DATE_FORMAT(`user_created_at` , '%Y-%m-%d') as `date`"))
            ->groupBy('date')
            ->get()->map(function ($value) {
                return (array)$value;
            })->keyBy('date')->toArray();
        foreach ($dates as $date)
        {
            if(!isset($list[$date]))
            {
                $list[$date] = array(
                    'date'=>$date,
                    'num'=>0,
                );
            }
        }
        $list = collect($list)->sortBy('date')->toArray();
        $dnu = collect($list)->pluck('num');
        $xAxis = array_keys($list);
        return compact('startTime'  , 'list' , 'dnu' , 'xAxis');
    }

    public function online(Request $request)
    {
        $appends = [];
        $keyword = $request->input('keyword' , '');
        $userId = -1;
        if($request->has('keyword'))
        {
            $appends['keyword'] = $keyword;
            $user = $this->user->allWithBuilder()->where('user_name' , $keyword)->first();
            $userId = blank($user)?0:$user->user_id;
        }
        $userId = intval($userId);
        $page = intval($request->input('page' , 1));
        if($userId>=0)
        {
            $result = $this->httpRequest('api/backstage/last/online' , array('user_id'=>join(',' , (array)$userId)) , "GET");
        }else{
            $max = $request->input('max' , Carbon::now('Asia/Shanghai')->timestamp);
            $appends['max'] = $max;
            $result = $this->httpRequest('api/backstage/last/online' , array('page'=>$page , 'max'=>$max), "GET");
        }
        if(is_array($result))
        {
            $activeUsers = $result['users'];
            $userIds = array_keys($activeUsers);
            $count = $result['count'];
            $perPage = $result['perPage'];
        }else{
            $activeUsers = $userIds = array();
            $count = 0;
            $perPage = 10;
        }
        $users = $this->user->findByMany($userIds);
        $users->each(function($user) use ($activeUsers){
            $user->activeTime = Carbon::createFromTimestamp($activeUsers[$user->user_id] , "Asia/Shanghai")->toDateTimeString();
        });
        $users = $this->paginator($users, $count, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ])->appends($appends);
        return view('backstage.passport.user.online' , compact('users' , 'appends'));
    }

    public function friendStatus(Request $request , $id)
    {
        return  Excel::download(new UsersFriendStatusExport($id), 'user-'.time().'.xlsx');
    }

    public function friendYesterdayStatus(Request $request , $id)
    {
        return  Excel::download(new UsersFriendYesterdayStatusExport($id), 'user-'.time().'.xlsx');
    }

    public function kol()
    {
        $kols = DB::connection('lovbee')->table('kol_users')->paginate(10);
        $userIds = $kols->pluck('user_id')->unique()->toArray();
        $users = DB::connection('lovbee')->table('users')->whereIn('user_id' , $userIds)->get();
        $kols->each(function($k , $i) use ($users){
            $k->user = $users->where('user_id' , $k->user_id)->first();
        });
        return view('backstage.passport.user.kol.index' , compact('kols'));
    }
    public function storeKol(Request $request)
    {
        $userId = $request->input('user_id' , '');
        if(empty($userId))
        {
            abort(422 , 'User ID cannot be empty');
        }
        $user = DB::connection('lovbee')->table('users')->where('user_id' , $userId)->first();
        if (empty($user)) {
            abort(404, 'The account does not exist');
        }
        $kolUser = DB::connection('lovbee')->table('kol_users')->where('user_id' , $userId)->first();
        if(empty($kolUser)) {
            DB::connection('lovbee')->table('kol_users')->insert(array(
                'user_id'=>$userId,
                'created_at'=>date('Y-m-d H:i:s'),
            ));
        }else{
            abort(422, 'Kol already exists!');
        }
        return response()->json(['result'=>'success']);
    }
    public function createKol(Request $request)
    {
        return view('backstage.passport.user.kol.create');
    }

}
