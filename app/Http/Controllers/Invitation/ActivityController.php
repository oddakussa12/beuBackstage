<?php

namespace App\Http\Controllers\Invitation;

use App\Models\Invitation\InviteEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
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

        $result = InviteEvent::orderByDesc('id')->paginate(10);
        $params['appends'] = $params;
        $params['data']    = $result;

        return view('backstage.invitation.activity.index' , $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        return view('backstage.invitation.activity.create', ['data' => null, 'counties'=>config('country')]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params = $request->all();

        Log::info('提交参数', $params);
        $this->validate($request, [
            'country'       => 'required|string',
            'first_register'=> 'required|int',
            'second'        => 'required|int',
            'seven'         => 'required|string',
            'thirty'        => 'required|string',
        ]);


        $result = InviteEvent::create($params);
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
        $data = InviteEvent::find($id);
        return view('backstage.invitation.activity.edit')->with(['data' => $data, 'counties'=>config('country')]);
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

        $this->validate($request, [
            'country'       => 'required|string',
            'first_register'=> 'required|int',
            'second'        => 'required|int',
            'seven'         => 'required|string',
            'thirty'        => 'required|string',
        ]);

        $param = $request->except('_token');
        InviteEvent::where('id', $id)->update($param);
        return response()->json(['result' => 'success']);
    }
}
