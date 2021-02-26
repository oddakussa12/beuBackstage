<?php

namespace App\Http\Controllers\Passport;

use App\Models\Passport\BlackUser;
use Excel;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Models\Passport\User;
use App\Models\Passport\Follow;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use App\Repositories\Contracts\UserRepository;
use App\Http\Requests\Passport\UpdateUserRequest;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function keep(Request $request)
    {
        $v = $request->input('v' , 0);
        if($v==1)
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
        return  view('backstage.passport.user.keep' , compact('period' , 'counties' , 'country_code' , 'list'));
    }

    public function keepV2(Request $request)
    {
        $list = array();
        $period = $request->input('period' , '');
        $country_code = strtolower(strval($request->input('country_code' , '')));
        $dates = array();
        if(!blank($period)&&!blank($country_code))
        {
            $connection = DB::connection('lovbee');
            list($start , $end) = explode(' - ' , $period);
            do{
                array_push($dates , $start);
                $start = Carbon::createFromFormat('Y-m-d' , $start)->addDays(1)->toDateString();
            }while($start <= $end);
            if(blank($country_code))
            {
                $list = $connection->table('data_retentions')
                    ->whereIn('date' , $dates)->orderBy('date')
                    ->select('date' , 'new as num' , '1 as tomorrowNum' , '2 as twoNum' , '3 as threeNum' , '7 as sevenNum' , '14 as fourteenNum' , '30 as thirtyNum')
                    ->get()->map(function ($value) {
                        return (array)$value;
                    })->groupBy('date')->toArray();
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
        return  view('backstage.passport.user.keep' , compact('period' , 'counties' , 'country_code' , 'list'));
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

}
