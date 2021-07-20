<?php

namespace App\Http\Controllers\Passport;

use App\Models\Passport\Group;


use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Application;


class GroupController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return void
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $group  = new Group();
        !empty($params['name'])     && $group = $group->where('name','like' , "%".$params['name']."%");
        !empty($params['dateTime']) && $group = $group->whereBetween('created_at', explode(' - ', $params['dateTime']));

        $where = [
            'administrator' => $params['administrator'] ?? '',
            'id' => $params['id'] ?? '',
        ];
        $where = array_filter($where);
        $order  = !empty($params['order'])&&in_array($params['order'] , array('created_at' , 'member'))?$params['order']:'created_at';
        !empty($where)&&$group  = $group->where($where);
        $groups = $group->orderByDesc($order)->paginate(10)->appends($params);
        foreach ($groups as $index=>$group) {
            $name   = json_decode($group->name, true);
            $avatar = json_decode($group->avatar, true);
            $groups[$index]->name = is_array($name) ? implode(',', array_values($name)) : $group->name;
            $groups[$index]->avatar = is_array($name) ? $avatar : [$group->avatar];
        }

        $params['appends'] = $params;
        $params['groups']  = $groups;


        return view('backstage.passport.group.index' , $params);
    }

    /**
     * @param $groupId
     * @return \Illuminate\Contracts\View\Factory|Application|\Illuminate\View\View
     * @throws \Throwable
     */
    public function member($groupId)
    {
        $members =  DB::connection('lovbee')->table('groups_members')->join('users', 'users.user_id', '=', 'groups_members.user_id')
            ->where('group_id', $groupId)->orderByDesc('groups_members.created_at')->paginate(10);
        $params['users'] = $members;
        return view('backstage.passport.group.member' , $params);
    }
}
