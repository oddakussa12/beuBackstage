<?php

namespace App\Http\Controllers\Invitation;

use App\Models\Invitation\Invitation;
use App\Models\Invitation\Score;
use App\Models\Invitation\ScoreBill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ScoreController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];
        $params = $request->all();
        $params['query'] = $query;

        $result = DB::connection('mt_front')->table('scores')
            ->join('users', 'scores.user_id', '=', 'users.user_id')
            ->join('invitation_code', 'scores.user_id', '=', 'invitation_code.user_id')
            ->leftJoin('users_phones', 'scores.user_id', '=', 'users_phones.user_id')
            ->select('scores.*', 'invitation_code.*', 'users.user_name', 'users.user_nick_name','users.user_avatar', 'users_phones.user_phone_country', 'users_phones.user_phone');

        $userCount  = Invitation::count();
        $totalScore = Score::sum(DB::raw('score+used_score'));
        $usedScore  = Score::sum('used_score');

        if(!empty($user_id)) {
            $result = $result->where('scores.user_id', $user_id);
        }
        if(!empty($username)) {
            $result = $result->where('users.user_name','like', "%$username%");
            $result = $result->where('users.user_nick_name','like', "%$username%");
        }

        $result = $result->paginate(10);
        $params['appends'] = $params;
        $params['data']    = $result;
        $params['total']   = [
            'userCount' => $userCount,
            'totalScore'=> $totalScore,
            'usedScore' => $usedScore,
        ];

        return view('backstage.invitation.score.index' , $params);
    }


    /**
     * @param $userId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 积分明细
     */
    public function score($userId)
    {
        $data = ScoreBill::where('user_id', $userId)->paginate(10);
        return view('backstage.invitation.score.score' , compact('data'));
    }

    /**
     * @param $userId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * 邀请明细
     */
    public function invite($userId)
    {
        $data = DB::connection('mt_front')->table('invitations')
            ->join('users', 'invitations.to_id', '=', 'users.user_id')
            ->where('invitations.from_id', $userId)
            ->select('users.*')->paginate(10);

        return view('backstage.invitation.score.invite' , compact('data'));

    }
}
