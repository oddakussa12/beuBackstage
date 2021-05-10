<?php

namespace App\Http\Controllers\Business;

use App\Models\Goods;
use App\Traits\PostTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GoodsController extends Controller
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
        $goods  = new Goods();
        $keyword= $params['keyword'] ?? '';
        if (isset($params['recommend'])) {
            $goods = $goods->where('recommend', $params['recommend']);
        }
        if (isset($params['status'])) {
            $goods = $goods->where('status', $params['status']);
        }
        if (!empty($params['dateTime'])) {
            $endDate = $now->endOfDay()->toDateTimeString();
            $allDate = explode(' - ' , $params['dateTime']);
            $start   = Carbon::createFromFormat('Y-m-d H:i:s' , array_shift($allDate))->subHours(8)->toDateTimeString();
            $end     = Carbon::createFromFormat('Y-m-d H:i:s' , array_pop($allDate))->subHours(8)->toDateTimeString();
            $start   = $start>$end ? $end : $start;
            $end     = $end>$endDate ? $endDate : $end;
            $goods   = $goods->where('created_at' , '>=' , $start)->where('created_at' , '<=' , $end);
        }
        if (!empty($keyword)) {
            $goods = $goods->where(function ($query) use ($keyword){
                $query->where('name', 'like', "%{$keyword}%");
            });
        }
        $goods = $goods->orderBy('created_at', 'DESC')->paginate(10);

        foreach ($goods as $good) {
            $good->image = !empty($good->image) && !is_array($good->image) ? json_decode($good->image, true) : $good->image;
        }
        $params['query']   = $query;
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
}
