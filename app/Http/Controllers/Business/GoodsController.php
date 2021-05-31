<?php

namespace App\Http\Controllers\Business;

use App\Models\Goods;
use App\Models\Passport\User;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GoodsController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $keyword= $params['keyword'] ?? '';
        $params = $request->all();
        $goods  = Goods::select(DB::raw('t_goods.*, t_users.user_name shop_name, t_users.user_nick_name shop_nick_name, t_goods_views.num view_num'))
            ->join('users', 'goods.user_id', '=', 'users.user_id')
            ->leftjoin('goods_views', 'goods_views.goods_id', '=', 'goods.id');
        if (isset($params['recommend'])) {
            $goods = $goods->where('goods.recommend', $params['recommend']);
        }
        $goods = $this->dateTime($goods, $params, 'addHours', 'goods');

        if (!empty($params['goods_id'])) {
            $goods = $goods->where('goods.id', $params['goods_id']);
        }
        if (!empty($keyword)) {
            $goods = $goods->where('goods.name', 'like', "%{$keyword}%");
        }
        if (!empty($params['shopName'])) {
            $shopName = $params['shopName'];
            $goods = $goods->where(function ($query) use ($shopName){
                $query->where('shops.name', 'like', "%{$shopName}%")->orWhere('nick_name', 'like', "%{$shopName}%");
            });
        }
        $sort  = !empty($params['sort']) ? $params['sort'] : 'created_at';
        $sort  = $sort == 'view_num' ? $sort : 'goods.'.$sort;
        $goods = $goods->groupBy('goods.id')->orderByDesc($sort)->paginate(10);

        $goodsIds = $goods->pluck('id')->toArray();
        $points   = DB::connection('lovbee')->table('goods_evaluation_points')->whereIn('goods_id', $goodsIds)->get();

        foreach ($goods as $good) {
            $good->image = !empty($good->image) && !is_array($good->image) ? json_decode($good->image, true) : $good->image;
            foreach ($points as $point) {
                if ($good->id==$point->goods_id) {
                    $good->score   = number_format((($point->point_1+$point->point_2*2+$point->point_3*3+$point->point_4*4+$point->point_5*5)/5), 2);
                }
            }
        }

        $params['appends'] = $params;
        $params['result']  = $goods;

        return view('backstage.business.goods.index' , $params);
    }

    public function update(Request $request, $id)
    {
        $params = $request->all();
        $goods  = Goods::find($id);
        if (!empty($params['recommend'])) {
            $goods->recommend = $params['recommend'] == 'on';
            $goods->recommended_at = date('Y-m-d H:i:s');
            $goods->save();
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
            $goods   = Goods::where('id', $id)->first();
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
                if ($item->goods_id==$goods->id) {
                    $item->goods_name = $goods->name;
                }
            }
        }

        $params['result'] = $result;
        return view('backstage.business.goods.view' , $params);
    }
}
