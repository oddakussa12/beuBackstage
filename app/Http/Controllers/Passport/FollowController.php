<?php

namespace App\Http\Controllers\Passport;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Models\Passport\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersFriendStatusExport;
use App\Repositories\Contracts\UserRepository;
use Illuminate\Database\Concerns\BuildsQueries;
use App\Exports\UsersFriendYesterdayStatusExport;

class FollowController extends Controller
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

    public function index(Request $request)
    {
        $follow_id = $request->input('follow_id' , 0);
        $appends['follow_id'] = $follow_id;
        $followed_id = $request->input('followed_id' , 0);
        $appends['followed_id'] = $followed_id;
        $follow_name = $request->input('follow_name' , '');
        $appends['follow_name'] = $follow_name;
        $params['follow_name'] = $follow_name;
        $followed_name = $request->input('followed_name' , '');
        $params['followed_name'] = $followed_name;
        $appends['followed_name'] = $followed_name;
        $dateTime = $request->input('dateTime' , ' - ');
        $params['dateTime'] = $dateTime;
        $appends['dateTime'] = $dateTime;
        $dateTime = $this->parseTime($dateTime);
        $connect = DB::connection('lovbee');
        $follows = $connect->table('users_follows');
        if(!empty($follow_id))
        {
            $follows = $follows->where('user_id' , $follow_id);
        }
        if(!empty($followed_id))
        {
            $follows = $follows->where('followed_id' , $followed_id);
        }
        if($dateTime!==false)
        {
            $follows = $follows->whereBetween('created_at' , array($dateTime['start'] , $dateTime['end']));
        }
        $follows = $follows->orderByDesc('created_at')->paginate(10)->appends($appends);
        $followIds = $follows->pluck('user_id')->toArray();
        $followedIds = $follows->pluck('followed_id')->toArray();
        $userIds = array_unique(array_merge($followIds,$followedIds));
        $users = $this->user->allWithBuilder()->whereIn('user_id' , $userIds)->get();
        $follows->each(function($f) use ($users){
            $f->follower = $users->where('user_id' , $f->user_id)->first();
            $f->followeder = $users->where('user_id' , $f->followed_id)->first();
        });
        $params['follows'] = $follows;
        $params['appends'] = $appends;
        return view('backstage.passport.follow.index' , $params);
    }


}
