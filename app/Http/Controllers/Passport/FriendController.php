<?php

namespace App\Http\Controllers\Passport;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepository;

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
        $countries = config('country');
        $params['countries']=$countries;
        $users = $this->user->friendManage($params);

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
        $dates  = printDates($start,$end);

        foreach ($dates as $date) {
            $res = collect($list)->where('date', $date)->pluck('num')->toArray();
            $num[] = !empty($res) ? current($res) : 0;
        }
        $params['dates']  = $dates;
        $params['line'][] = [
            "name" => 'Friend Count',
            "type" => "line",
            "data" => $num ?? [],
            'markPoint' => ['data' =>[['type'=>'max', 'name'=>'MAX'], ['type'=>'min', 'name'=>'MIN']]],
            'markLine'  => ['data' =>[['type'=>'average']]],
            'itemStyle' => ['normal'=>['label'=>['show'=>true]]]
        ];
        return view('backstage.passport.friend.index', $params);
    }

    public function request(Request $request)
    {
        $appends = array();
        $from = strval($request->input('from' , ''));
        $to = strval($request->input('to' , ''));
        $connection = DB::connection('lovbee');
        $requests = $connection->table('friends_requests');
        if(!blank($from))
        {
            $appends['from'] = $from;
            $sender = $connection->table('users')->where('user_name' , $from)->first();
            if(blank($sender))
            {
                $sender = $connection->table('users')->where('user_nick_name' , $from)->first();
            }
            if(blank($sender))
            {
                $requests->where('request_from_id' , 0);
            }else{
                $requests->where('request_from_id' , $sender->user_id);
            }
        }

        if(!blank($to))
        {
            $appends['to'] = $to;
            $target = $connection->table('users')->where('user_name' , $to)->first();
            if(blank($target))
            {
                $target = $connection->table('users')->where('user_nick_name' , $from)->first();
            }
            if(blank($target))
            {
                $requests->where('request_to_id' , 0);
            }else{
                $requests->where('request_to_id' , $target->user_id);
            }
        }
        $users = collect();
        $requests = $requests->orderByDesc('request_created_at')->paginate(10);
        $sendIds = $requests->pluck('request_from_id')->toArray();
        $targetIds = $requests->pluck('request_to_id')->toArray();
        $userIds = array_unique(array_merge($sendIds , $targetIds));
        !blank($userIds)&&$users = $connection->table('users')->whereIn('user_id' , $userIds)->get();
        $requests->each(function($item , $index) use ($users){
            $item->from = $users->where('user_id' , $item->request_from_id)->values()->first();
            $item->to = $users->where('user_id' , $item->request_to_id)->values()->first();
            $item->request_created_at = Carbon::createFromTimestamp($item->request_created_at , 'Asia/Shanghai')->toDateTimeString();
            $item->request_updated_at = Carbon::createFromTimestamp($item->request_updated_at , 'Asia/Shanghai')->toDateTimeString();
        });
        return view('backstage.passport.friend.request', compact('requests' , 'from' , 'to' , 'appends'));
    }


}
