<?php

namespace App\Http\Controllers\Invitation;

use App\Models\Invitation\Goods;
use App\Models\Invitation\InviteEvent;
use App\Models\Invitation\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoodController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];
        $params = $request->all();
        $params['query'] = $query;

        /*$result = DB::connection('mt_front')->table('goods')
            ->leftJoin('orders', 'goods.id', '=', 'orders.good_id')
            ->select('goods.*', DB::raw("count(f_orders.good_id) num"))->groupBy('orders.good_id')->paginate(10);*/

        $goods   = Goods::paginate(10);
        $goodIds = $goods->pluck('id');
        $order = Order::select('good_id', DB::raw('count(1) as num'))->whereIn('good_id', $goodIds)->groupBy('good_id')->get();

        foreach ($goods as $good) {
            $good->num = 0;
            foreach ($order as $item) {
                if ($good->id==$item->good_id) {
                    $good->num = $item->num;
                }
            }
        }

        $params['appends'] = $params;
        $params['data']    = $goods;

        return view('backstage.invitation.good.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        return view('backstage.invitation.good.create', ['data' => null, 'counties'=>config('country')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Log::info('提交参数', $request->all());
        $this->validate($request, [
            'country'       => 'required|string',
            'name'          => 'required|string',
            'total'         => 'required|string',
            'image'         => 'required|string',
        ]);

        $params = $request->except('_token');
        $result = Goods::create($params);
        return response()->json([
            'result' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = Goods::find($id);
        return view('backstage.invitation.good.edit')->with(['data' => $data, 'counties'=>config('country')]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $image  = $request->input('image');
        $status = $request->input('status');
        $param  = $request->except('_token');

        if(!in_array($status, ['on', 'off'])) {
            if (empty($image)) {
                $this->validate($request, [
                    'country'       => 'required|string',
                    'name'          => 'required|string',
                    'total'         => 'required|string',
                ]);
            }
        } else {
            $param['status'] = $status=='on' ? 1 : 0;
        }
        Goods::where('id', $id)->update($param);
        return response()->json(['result' => 'success']);
    }

}
