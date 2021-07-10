<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Models\Passport\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GoodsCategoryController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        $goodsCategories = DB::connection('lovbee')->table('goods_categories');
        if(isset($params['default'])) {
            $goodsCategories = $goodsCategories->where('default', $params['default']);
        }
        if(!empty($params['dateTime']))
        {
            $date = $this->parseTime($params['dateTime']);
            if($date!=false)
            {
                $goodsCategories = $goodsCategories->whereBetween('created_at' , array($date['start'] , $date['end']));
            }
        }
        $goodsCategories = $goodsCategories->orderByDesc('created_at')->paginate(10);
        $userIds= $goodsCategories->pluck('user_id')->unique()->toArray();
        $users = User::whereIn('user_id', $userIds)->get();
        $goodsCategories->each(function($goodsCategory) use ($users){
            $goodsCategory->user = $users->where('user_id' , $goodsCategory->user_id)->first();
        });
        $params['goodsCategories'] = $goodsCategories;
        return view('backstage.business.goods_category.index', $params);

    }

}
