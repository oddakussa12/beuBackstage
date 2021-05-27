<?php

namespace App\Http\Controllers\Business;

use App\Models\Goods;
use App\Models\Passport\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{

    public function base(Request $request)
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];
        $params = $request->all();
        $result = DB::connection('lovbee')->table('comments')->where('step', 1);
        if (isset($params['recommend'])) {
            $result = $result->where('level', $params['recommend']);
        }
        if (isset($params['verify'])) {
            $result = $result->where('verified', $params['verify']);
        }

        $result = $this->dateTime($result, $params, 'addHours', 'comments');

        if (!empty($params['goods_id'])) {
            $result = $result->where('goods_id', $params['goods_id']);
        }
        if (!empty($params['shopName'])) {
            $shopName = $params['shopName'];
            $user   = User::where('user_name', $shopName)->orWhere('user_nick_name', $shopName)->get();
            $userIds= $user->pluck('user_id')->toArray();
            $result = $result->whereIn('owner', $userIds);
        }
        if (!empty($params['keyword'])) {
            $goods    = Goods::where('name', $params['keyword'])->get();
            $goodsIds= $goods->pluck('id')->toArray();
            $result  = $result->whereIn('goods_id', $goodsIds);
        }
        $sort   = !empty($params['sort']) && $params['sort'] == 'asc' ? 'orderBy' : 'orderByDesc';
        $result = $result->$sort('created_at')->paginate(10);
        $params['query']   = $query;

        return $this->select($result, $params);
    }

    public function select($result, $params)
    {
        $userIds  = $result->pluck('user_id')->unique()->toArray();
        $toIds    = $result->pluck('to_id')->unique()->toArray();
        $topIds   = $result->pluck('top_id')->unique()->toArray();
        $shopIds  = $result->pluck('owner')->unique()->toArray();
        $goodsIds = $result->pluck('goods_id')->unique()->toArray();
        $ids      = array_unique(array_merge($userIds, $shopIds, $toIds, $topIds));
        $users    = User::whereIn('user_id', $ids)->get();
        $goods    = Goods::whereIn('id', $goodsIds)->get();

        foreach ($result as $item) {
            foreach ($users as $user) {
                if ($item->user_id==$user->user_id) {
                    $item->user_nick_name = $user->user_nick_name;
                }
                if ($item->to_id==$user->user_id) {
                    $item->to_nick_name = $user->user_nick_name;
                }
                if ($item->top_id==$user->user_id) {
                    $item->top_nick_name = $user->user_nick_name;
                }
                if ($item->owner==$user->user_id) {
                    $item->shop_nick_name = $user->user_nick_name;
                }
            }
            foreach ($goods as $good) {
                if ($item->goods_id==$good->id) {
                    $item->goods_name = $good->name;
                    $item->image = $good->image;
                }
            }
            if (!empty($item->media)) {
                $item->media = json_decode($item->media, true);
            }
        }

        $params['appends'] = $params;
        $params['result']  = $result;

        return $params;
    }
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $params = $this->base($request);
        return view('backstage.business.review.index' , $params);
    }

    /*public function audit(Request $request)
    {
        request()->offsetSet('verify', -1);
        $params = $this->base($request);
        return view('backstage.business.review.audit', $params);
    }*/

    public function audit(Request $request)
    {
        return $this->flow($request);
    }

    public function flow(Request $request)
    {
        request()->offsetSet('verify', -1);
        $params = $request->all();
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];

        $adminId = auth()->user()->admin_id;
        $today   = date('Y-m-d 00:00:00');
        $claim   = DB::table('comments_claim')->where('admin_id', $adminId)->first();
        $cCount  = DB::table('comments_claim')->where('admin_id', $adminId)->count();
        if ($claim) {
            $list  = DB::connection('lovbee')->table('comments')->where(['verified'=>-1, 'step'=>1])->where('comment_id', $claim->comment_id)->get();
            $list  = $this->select($list, $params);
            $list  = collect($list['result'])->first();
            $result= collect($list)->toArray();
        }
        $count     = DB::connection('lovbee')->table('comments')->where(['verified'=>-1])->count();
        $totayCount= DB::table('business_audits')->where('admin_id', $adminId)->where('created_at', '>=', $today)->count();
        $totalCount= DB::table('business_audits')->where('admin_id', $adminId)->count();

        if (!empty($result)) {
            $result = collect($result)->toArray();
            $result['todayCount'] = $totayCount ?? 0;
            $result['totalCount'] = $totalCount ?? 0;
        }
        $result['claim']     = $cCount;
        $result['unaudited'] = $count;
        $result['query']     = $query;

        return view('backstage.business.review.flow', compact('result'));
    }

    /**
     * 领取审核帖子
     */
    public function claim()
    {
        $adminId = auth()->user()->admin_id;
        $result  = DB::table('comments_claim')->where('admin_id', $adminId)->first();
        if (empty($result)) {
            $claim = DB::table('comments_claim')->get();
            $ids   = $claim->pluck('comment_id')->toArray();
            $list  = DB::connection('lovbee')->table('comments')->where('verified', -1)->whereNotIn('comment_id', $ids)->orderByDesc('created_at')->limit(10)->get();
            $data  = [];
            foreach ($list as $item) {
                $data[] = ['admin_id' => $adminId, 'comment_id' => $item->comment_id, 'created_at'=>date('Y-m-d H:i:s')];
            }
            DB::table('comments_claim')->insert($data);
        }
        return redirect(route('business::review.audit'));
    }

    public function auditLog($id, $params)
    {
        $data = [
            'audit_id'  => $id,
            'type'      => 'comment',
            'date'      => date('Y-m-d'),
            'status'    => $params['audit'],
            'admin_id'  => auth()->user()->admin_id,
            'created_at'=> date('Y-m-d H:i:s'),
            'admin_username' => auth()->user()->admin_username,
        ];
        DB::table('business_audits')->insert($data);

    }

    public function update(Request $request, $id)
    {
        $params = $request->all();
        if (!empty($params['audit'])) {
            $verify = $params['audit'] == 'pass' ? 1 : 0;
            $url    = $verify ? '/api/backstage/review/comment' : '/api/backstage/reject/comment';
            $result = $this->httpRequest($url, ['id'=>$id, 'reviewer'=>auth()->user()->admin_id]);
        }
        if (!empty($params['level'])) {
            $level = $params['level'] == 'on' ? 1 : 0;
            DB::connection('lovbee')->table('comments')->where('comment_id', $id)->update(['level'=>$level]);
        }
        return redirect(route('business::review.audit'));
    }

    public function view(Request $request, $id)
    {
        $params = $request->all();
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];

        $sort   = !empty($params['sort']) && $params['sort'] == 'asc' ? 'orderBy' : 'orderByDesc';
        $result = DB::connection('lovbee')->table('comments')->where('top_id', $id);
        $result = $this->dateTime($result, $params);
        $result = $result->$sort('created_at')->paginate(10);

        $params = $this->select($result, $params);
        $params['query']   = $query;

        return view('backstage.business.review.view' , $params);
    }

}
