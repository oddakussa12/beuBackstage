<?php

namespace App\Http\Controllers\Passport;

use App\Models\Passport\Group;
use App\Models\Passport\GroupTopic;
use Excel;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Models\Passport\User;
use App\Models\Passport\Follow;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use App\Http\Requests\Passport\UpdateUserRequest;

class GroupController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $params = $request->all();
        $group  = new Group();

        !empty($params['name'])     && $group = $group->where('name','like' , "%".$params['name']."%");
        !empty($params['dateTime']) && $group = $group->whereBetween('created_at', explode(' - ', $params['dateTime']));

        $where = [
            'administrator' => $params['owner_id'] ?? '',
            'id' => $params['id'] ?? '',
        ];
        $where = array_filter($where);

        $order  = !empty($params['order']) ? 'member' : 'created_at';
        $group  = $group->where($where)->orderByDesc($order);
        $groups = $group->paginate(10);
        foreach ($groups as $index=>$group) {
            $name   = json_decode($group->name, true);
            $avatar = json_decode($group->avatar, true);
            $groups[$index]->name = is_array($name) ? implode(',', array_values($name)) : $group->name;
            $groups[$index]->avatar = is_array($name) ? $avatar : [$group->avatar];
        }

        $params['appends'] = $params;
        $params['groups']  = $groups;
        $params['query']   = $uri['query'] ?? '';

        return view('backstage.passport.group.index' , $params);
    }

    /**
     * @param $groupId
     * @return \Illuminate\Contracts\View\Factory|Application|\Illuminate\View\View
     */
    public function member($groupId)
    {
        $members =  DB::connection('lovbee')->table('groups_members')->join('users', 'users.user_id', '=', 'groups_members.user_id')
            ->where('group_id', $groupId)->orderByDesc('groups_members.created_at')->paginate(10);
        $params['users'] = $members;
        return view('backstage.passport.group.member' , $params);
    }
}
