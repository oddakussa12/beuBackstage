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
                    ->orderByDesc('user_id')->chunk(200 , function ($users) use ($connection , $start , $tz , &$num , &$tomorrowNum , &$twoNum , &$threeNum , &$sevenNum , &$thirtyNum){
                        $num = $num+count($users);
                        $userIds = $users->pluck('user_id')->all();
                        $tomorrow = Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(1);
                        $s = $tomorrow->startOfDay()->timestamp;
                        $e = $tomorrow->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s)->format('Ym');
                        $nm = Carbon::createFromTimestamp($e)->format('Ym');
                        if($pm==$nm)
                        {
                            $tomorrowTable = 'visit_logs_'.$pm;
                            $tomorrowT = $connection->table($tomorrowTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $tomorrow->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $tomorrow->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $tomorrowPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $pm)->count(DB::raw('DISTINCT(user_id)'));
                            $tomorrowNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $nm)->count(DB::raw('DISTINCT(user_id)'));
                            $tomorrowT = $tomorrowPT+$tomorrowNT;
                        }
                        $tomorrowNum = $tomorrowNum+$tomorrowT;



                        $twoDays = Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(2);
                        $s = $twoDays->startOfDay()->timestamp;
                        $e = $twoDays->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s)->format('Ym');
                        $nm = Carbon::createFromTimestamp($e)->format('Ym');
                        if($pm==$nm)
                        {
                            $twoDaysTable = 'visit_logs_'.$pm;
                            $twoDaysT = $connection->table($twoDaysTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $twoDays->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $twoDays->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $twoDaysPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $pm)->count(DB::raw('DISTINCT(user_id)'));
                            $twoDaysNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $nm)->count(DB::raw('DISTINCT(user_id)'));
                            $twoDaysT = $twoDaysPT+$twoDaysNT;
                        }
                        $twoNum = $twoNum+$twoDaysT;



                        $threeDays= Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(3);
                        $s = $threeDays->startOfDay()->timestamp;
                        $e = $threeDays->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s)->format('Ym');
                        $nm = Carbon::createFromTimestamp($e)->format('Ym');
                        if($pm==$nm)
                        {
                            $threeDaysTable = 'visit_logs_'.$pm;
                            $threeDaysT = $connection->table($threeDaysTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $threeDays->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $threeDays->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $threeDaysPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $pm)->count(DB::raw('DISTINCT(user_id)'));
                            $threeDaysNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $nm)->count(DB::raw('DISTINCT(user_id)'));
                            $threeDaysT = $threeDaysPT+$threeDaysNT;
                        }
                        $threeNum = $threeNum+$threeDaysT;



                        $sevenDays= Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(7);
                        $s = $sevenDays->startOfDay()->timestamp;
                        $e = $sevenDays->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s)->format('Ym');
                        $nm = Carbon::createFromTimestamp($e)->format('Ym');
                        if($pm==$nm)
                        {
                            $sevenDaysTable = 'visit_logs_'.$pm;
                            $sevenDaysT = $connection->table($sevenDaysTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $sevenDays->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $sevenDays->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $sevenDaysPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $pm)->count(DB::raw('DISTINCT(user_id)'));
                            $sevenDaysNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $nm)->count(DB::raw('DISTINCT(user_id)'));
                            $sevenDaysT = $sevenDaysPT+$sevenDaysNT;
                        }
                        $sevenNum = $sevenNum+$sevenDaysT;



                        $thirtyDays= Carbon::createFromFormat('Y-m-d' , $start , $tz)->addDays(30);
                        $s = $thirtyDays->startOfDay()->timestamp;
                        $e = $thirtyDays->endOfDay()->timestamp;
                        $pm = Carbon::createFromTimestamp($s)->format('Ym');
                        $nm = Carbon::createFromTimestamp($e)->format('Ym');
                        if($pm==$nm)
                        {
                            $thirtyDaysTable = 'visit_logs_'.$pm;
                            $thirtyDaysT = $connection->table($thirtyDaysTable)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $thirtyDays->startOfDay()->timestamp)
                                ->where('visited_at' , '<=' , $thirtyDays->endOfDay()->timestamp)->count(DB::raw('DISTINCT(user_id)'));
                        }else{
                            $thirtyDaysPT = $connection->table('visit_logs_'.$pm)->whereIn('user_id' , $userIds)->where('visited_at' , '>=' , $pm)->count(DB::raw('DISTINCT(user_id)'));
                            $thirtyDaysNT = $connection->table('visit_logs_'.$nm)->whereIn('user_id' , $userIds)->where('visited_at' , '<=' , $nm)->count(DB::raw('DISTINCT(user_id)'));
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
                    'thirtyNum'=>$thirtyNum,
                );
//                Log::info('test' , array('$start:'.$start.' $num:'.$num.' $tomorrowNum:'.$tomorrowNum.' $twoNum:'.$twoNum.' $threeNum:'.$threeNum.' $sevenNum:'.$sevenNum.' $thirtyNum:'.$thirtyNum));
                $start = Carbon::createFromFormat('Y-m-d' , $start)->addDays(1)->toDateString();
            }while ($start != $end);
        }
        $counties = config('country');
        return  view('backstage.passport.user.keep' , compact('period' , 'counties' , 'country_code' , 'list'));
    }

}
