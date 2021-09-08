<?php

namespace App\Http\Controllers\Business;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DeliveryCostController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $costs = DB::connection('lovbee')->table('delivery_costs')->orderBy('distance')->get();
        return view('backstage.business.delivery_cost.index' , compact('costs'));
    }

    public function store(Request $request)
    {
        $this->validate($request , array(
            'distance'=>array(
                'required',
                'numeric',
                'min:0',
            ),
            'cost'=>array(
                'required',
                'numeric',
                'min:0',
            )
        ));
        $distance = (float)$request->input('distance' , 0);
        $cost = (float)$request->input('cost' , 0);
        $deliveryCost = DB::connection('lovbee')->table('delivery_costs');
        $exist = $deliveryCost->where('distance' , $distance)->first();
        if(!empty($exist))
        {
            abort(403 , 'The distance range already exists!');
        }
        $deliveryCost->insert(array(
            'distance'=>$distance,
            'cost'=>$cost,
        ));
        return response()->json(array(
            'result'=>'success'
        ));
    }

    public function update(Request $request , $id)
    {
        $data = array();
        $deliveryCost = DB::connection('lovbee')->table('delivery_costs');
        if($request->has('distance'))
        {
            $this->validate($request , array(
                'distance'=>array(
                    'required',
                    'numeric',
                    'min:1',
                )
            ));
            $cost = $deliveryCost->where('id' , $id)->first();
            if($cost->distance==='∞')
            {
                abort(403 , 'This item cannot be updated!');
            }
            $distance = (float)$request->input('distance' , 0);
            $exist = $deliveryCost->where('distance' , $distance)->first();
            if(!empty($exist))
            {
                abort(403 , 'The distance range already exists!');
            }
            $data['cost'] = $distance;
        }
        if($request->has('cost'))
        {
            $this->validate($request , array(
                'cost'=>array(
                    'required',
                    'numeric',
                    'min:0',
                )
            ));
            $cost = (float)$request->input('cost' , 0);
            $data['cost'] = $cost;
        }
        !empty($data)&&$deliveryCost->where('id' , $id)->update($data);
        return response()->json(array(
            'result'=>'success'
        ));
    }

    public function destroy($id)
    {
        $deliveryCost = DB::connection('lovbee')->table('delivery_costs');
        $cost = $deliveryCost->where('id' , $id)->first();
        if(!empty($cost)&&$cost->distance==="∞")
        {
            abort(403 , 'This item cannot be deleted!');
        }
        DB::connection('lovbee')->table('delivery_costs')->where('id' , $id)->delete();
        return response()->json(array(
            'result'=>'success'
        ));
    }

}
