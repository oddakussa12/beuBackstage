<?php

namespace App\Http\Controllers\Business;

use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];
        $params = $request->all();
        $now    = Carbon::now();
        $shop   = new Shop();
        $keyword= $params['keyword'] ?? '';
        if (isset($params['recommend'])) {
            $shop = $shop->where('recommend', $params['recommend']);
        }
        if (isset($params['level'])) {
            $shop = $shop->where('level', $params['level']);
        }
        if (!empty($params['dateTime'])) {
            $endDate = $now->endOfDay()->toDateTimeString();
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
            $start   = $start>$end ? $end : $start;
            $end     = $end>$endDate ? $endDate : $end;
            $shop = $shop->where('created_at' , '>=' , $start)->where('created_at' , '<=' , $end);
        }
        if (!empty($keyword)) {
            $shop = $shop->where(function ($query) use ($keyword){
                $query->where('name', 'like', "%{$keyword}%")->orWhere('nick_name', 'like', "%{$keyword}%");
            });
        }
        $shops = $shop->orderBy('created_at', 'DESC')->paginate(10);

        $params['query']   = $query;
        $params['appends'] = $params;
        $params['result']  = $shops;

        return view('backstage.business.shop.index' , $params);
    }

    public function update(Request $request, $id)
    {
        $params = $request->all();
        $shop   = Shop::find($id);
        if (!empty($params['recommend'])) {
            $shop->recommend = $params['recommend'] == 'on';
            $shop->recommended_at = date('Y-m-d H:i:s');
        }
        if (isset($params['level'])) {
            $shop->level = $params['level'] == 'on';
        }
        $shop->save();
        return [];
    }

}
