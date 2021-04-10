<?php
namespace App\Http\Controllers\Operator;

use App\Exports\MessageExport;
use App\Models\Passport\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class VirtualUserController extends Controller
{
    public function index()
    {
        $userIds = DB::connection('lovbee')->table('users_virtual')->pluck('user_id')->toArray();
        $users   = User::whereIn('user_id', $userIds)->select('user_id', 'user_avatar', 'user_name', 'user_nick_name', 'user_created_at')->paginate(10);
        $result  = $this->httpRequest('api/backstage/score', [], "GET");
        foreach ($users as $user) {
            $i=1;
            foreach ($result as $key=>$item) {
                if ($user->user_id==$key) {
                    $user->score = $item;
                    $user->rank  = $i;
                }
                $i++;
            }
        }
        return view('backstage.operator.virtual.index', compact('users'));
    }

    public function create()
    {
        return view('backstage.operator.virtual.create');
    }

    public function store(Request $request)
    {
        $userName = $request->input('user_name');
        $userName = trim($userName);
        if (empty($userName)) {
            return ['code'=>1, 'message'=>'ID cannot be empty'];
        }
        $user = User::where('user_name', $userName)->first();
        if (empty($user)) {
            return ['code'=>1, 'message'=>'Account does not exist'];
        }
        $virtual = DB::connection('lovbee')->table('users_virtual')->where('user_id', $user->user_id)->first();
        if (!empty($virtual)) {
            return ['code'=>1, 'message'=>'Account to repeat'];
        }
        $insert = DB::connection('lovbee')->table('users_virtual')->insert(['user_id'=>$user->user_id, 'created_at'=>date('Y-m-d H:i:s')]);
        if ($insert) {
            return [];
        } else {
            return ['code'=>1, 'message'=>'Please retry a little later'];
        }
    }

    public function update(Request $request, $id)
    {
        $score  = $request->input('score');
        if ($score<=0) {
            return [];
        }

        $result = $this->httpRequest('api/backstage/score', ['id'=>$id, 'score'=>$score]);

        return $result;

    }

}