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
        $result  = DB::connection('lovbee')->table('comments')->where('step', 1);
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

        $userIds  = $result->pluck('user_id')->unique()->toArray();
        $toIds    = $result->pluck('to_id')->unique()->toArray();
        $shopIds  = $result->pluck('owner')->unique()->toArray();
        $goodsIds = $result->pluck('goods_id')->unique()->toArray();

        $users    = User::whereIn('user_id', array_unique(array_merge($userIds, $shopIds, $toIds)))->get();
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

        $params['query']   = $query;
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

    public function audit(Request $request)
    {
        $params = $this->base($request);
        return view('backstage.business.review.audit', $params);
    }


    public function update(Request $request, $id)
    {
        $params = $request->all();
        $base   = DB::connection('lovbee')->table('comments')->where('comment_id', $id);
        if (!empty($params['audit'])) {
            $verify = $params['audit'] == 'pass' ? 1 : 0;
            $base->update(['verified'=>$verify, 'verified_at'=>date('Y-m-d H:i:s')]);
        }
        if (!empty($params['level'])) {
            $level = $params['level'] == 'on' ? 1 : 0;
            $base->update(['level'=>$level]);
        }
        return [];
    }

    public function view(Request $request, $id)
    {
        $params = $request->all();
        $sort   = !empty($params['sort']) && $params['sort'] == 'asc' ? 'orderBy' : 'orderByDesc';
        $result = DB::connection('lovbee')->table('comments')->where('top_id', $id);
        $result = $this->dateTime($result, $params);
        $result = $result->$sort('created_at')->paginate(10);

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
        return view('backstage.business.review.view' , $params);
    }
}
