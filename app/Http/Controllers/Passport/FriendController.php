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


}
