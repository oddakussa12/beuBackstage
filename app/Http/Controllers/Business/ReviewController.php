<?php

namespace App\Http\Controllers\Business;

use App\Models\Goods;
use App\Models\Passport\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];
        $keyword= $params['keyword'] ?? '';
        $params = $request->all();
        $result  = DB::connection('lovbee')->table('comments')->select(DB::raw('t_comments.*'));
        if (isset($params['level'])) {
            $result = $result->where('comments.level', $params['level']);
        }
        $result = $this->dateTime($result, $params, 'addHours', 'comments');

        if (!empty($params['goods_id'])) {
            $result = $result->where('goods.id', $params['goods_id']);
        }
        if (!empty($params['shopName'])) {
            $shopName = $params['shopName'];
            $user   = User::where('user_name', $shopName)->orWhere('user_nick_name', $shopName)->get();
            $userIds= $user->pluck('user_id')->toArray();
            $result = $result->whereIn('shop_id', $userIds);
        }
        if (!empty($params['keyword'])) {
            $user    = Goods::where('name', $keyword)->get();
            $goodsIds= $user->pluck('id')->toArray();
            $result  = $result->whereIn('goods_id', $goodsIds);
        }
        $sort   = !empty($params['sort']) ? $params['sort'] : 'created_at';
        $sort   = $sort == 'view_num' ? $sort : 'comments.'.$sort;
        $result = $result->orderByDesc($sort)->paginate(10);

        $userIds  = $result->pluck('user_id')->unique()->toArray();
        $toIds    = $result->pluck('to_id')->unique()->toArray();
        $shopIds  = $result->pluck('owner')->unique()->toArray();
        $goodsIds = $result->pluck('goods_id')->unique()->toArray();
        $mediaIds = $result->where('media', '!=', '')->pluck('comment_id')->toArray();

        $users = User::whereIn('user_id', array_unique(array_merge($userIds, $shopIds, $toIds)))->get();
        $goods = Goods::whereIn('id', $goodsIds)->get();
        $media = DB::connection('lovbee')->table('comments_media')->whereIn('comment_id', $mediaIds)->get();
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
            foreach ($media as $m) {
                if ($item->comment_id==$m->comment_id) {
                    $item->media_url = $m->url;
                }
            }
        }

        $params['query']   = $query;
        $params['appends'] = $params;
        $params['result']  = $result;


        return view('backstage.business.review.index' , $params);
    }

    public function update(Request $request, $id)
    {
        $params = $request->all();
        $result  = DB::connection('lovbee')->table('comments')->find($id);
        if (!empty($params['verify'])) {
            $result->recommend = $params['recommend'] == 'on';
            $result->recommended_at = date('Y-m-d H:i:s');
            $result->save();
        }
        return [];
    }

    public function view(Request $request, $id)
    {
        $params = $request->all();
        $result = DB::connection('lovbee')->table('goods_views_logs')->where('goods_id', $id);
        $result = $this->dateTime($result, $params);
        $result = $result->paginate(10);

        if ($result->isNotEmpty()) {
            $userIds = $result->pluck('user_id')->unique()->toArray();
            $shopIds = $result->pluck('owner')->unique()->toArray();
            $userIds = array_unique(array_merge($userIds, $shopIds));
            $users   = User::select('user_id', 'user_name', 'user_nick_name')->whereIn('user_id', $userIds)->get();
            $result   = Goods::where('id', $id)->first();
            foreach ($result as $item) {
                foreach ($users as $user) {
                    if ($item->user_id==$user->user_id) {
                        $item->user_name = $user->user_name;
                        $item->user_nick_name = $user->user_nick_name;
                    }
                    if ($item->owner==$user->user_id) {
                        $item->shop_name = $user->user_name;
                        $item->shop_nick_name = $user->user_nick_name;
                    }
                }
                if ($item->goods_id==$result->id) {
                    $item->goods_name = $result->name;
                }
            }
        }

        $params['result'] = $result;
        return view('backstage.business.goods.view' , $params);
    }
}
