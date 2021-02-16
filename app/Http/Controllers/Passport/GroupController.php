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
        // $uri    = parse_url($request->server('REQUEST_URI'));
        $params = $request->all();
        $group  = new Group();

        !empty($params['name'])     && $group = $group->where('name','like' , "%".$params['name']."%");
        !empty($params['dateTime']) && $group = $group->whereBetween('created_at', explode(' - ', $params['dateTime']));

        $where = [
            'owner_id'   => $params['owner_id'] ?? '',
            'group_id'   => $params['id'] ?? '',
            'type'       => $params['type'] ?? '',
        ];
        $where = array_filter($where);
        if (!empty($params['topic_name'])) {
            $groupIds = GroupTopic::where('topic_name', 'like', "%{$params['topic_name']}%")->pluck('group_id')->toArray();
            $group    = $group->whereIn('group_id', $groupIds);
//            $group = $group->with(['topic'=>function($q) use ($params) {$q->where('groups_topics.topic_name', 'like', "%{$params['topic_name']}%");}]);
        }

        $order  = !empty($params['order']) ? 'member_count' : 'id';
        $group  = $group->with('topic')->where($where)->orderByDesc($order);
        $groups = $group->paginate(10);

        $params['appends'] = $params;
        $params['groups']  = $groups;
        $params['query']   = $uri['query'] ?? '';

        return view('backstage.passport.group.index' , $params);
    }

    /**
     * @param $groupId
     * @param $status
     * @return Response
     * 群是否设置退推荐群
     */
    public function update($groupId, $status)
    {
        $switch = $status=='on' ? 1 : 0;
        Group::where(['id'=>$groupId])->update(['is_recommend'=>$switch]);
        return response()->json(['result' => 'success']);
    }

}
