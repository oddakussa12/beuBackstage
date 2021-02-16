<?php

namespace App\Http\Controllers\Invitation;

use App\Models\Invitation\InviteEvent;
use App\Models\Invitation\Order;
use App\Models\Invitation\OrderHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];
        $params = $request->all();
        $params['query'] = $query;
        $result = new Order();

        if (isset($params['status'])) {
            $result = $result->where('status', $params['status']);
        }
        if (!empty($params['user_id'])) {
            $result = $result->where('user_id', $params['user_id']);
        }
        if (!empty($params['user_name'])) {
            $result = $result->where('user_name', 'like', "%{$params['user_name']}%");
        }
        if (!empty($params['phone'])) {
            $result = $result->where('phone', 'like', "%{$params['phone']}%");
        }
        if (!empty($params['good_id'])) {
        $result = $result->where('good_id', $params['good_id']);
        }
        if (!empty($params['dateTime'])) {
            $result = $result->whereBetween('created_at', explode(' - ', $params['dateTime']));
        }

        $result = $result->orderByDesc('id')->paginate(10);
        $params['appends'] = $params;
        $params['data']    = $result;

        return view('backstage.invitation.order.index' , $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = InviteEvent::find($id);
        return view('backstage.invitation.order.edit')->with(['data' => $data]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $order  = Order::find($id)->toArray();
        $status = $request->input('status');
        $flag   = $status==='on';
        Order::where('id', $id)->update(['status'=>$flag, 'updated_at'=>date('Y-m-d H:i:s')]);

        $order['order_id'] = $order['id'];
        $order['status']   = $flag;
        $order['operator'] = auth()->user()->admin_username;

        unset($order['id'], $order['created_at'], $order['updated_at']);
        OrderHistory::create($order);
        return response()->json(['result' => 'success']);
    }
}
