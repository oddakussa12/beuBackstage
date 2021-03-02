<?php

namespace App\Http\Controllers\Props;

use App\Http\Controllers\Controller;
use App\Models\Props;
use App\Models\PropsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PropsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $uri    = parse_url($request->server('REQUEST_URI'));
        $query  = empty($uri['query']) ? "" : $uri['query'];
        $params = $request->all();
        $params['query'] = $query;
        $goods   = Props::orderByDesc('id')->paginate(10);
        $params['appends'] = $params;
        $params['data']    = $goods;

        return view('backstage.props.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $category = PropsCategory::whereNull('deleted_at')->get();
        return view('backstage.props.create', ['data' => null, 'categories'=>$category]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $params = $request->except('_token');

        Log::info('提交参数', $request->all());
        $this->validate($request, [
            'name'  => 'required|string',
            'cover' => 'required|string',
            'url'   => 'required|string',
            'hash'  => 'required|string',
        ]);

        if (!empty($params['hash'])) {
            $params['hash'] = strtolower($params['hash']);
        }
        $result = Props::create($params);
        return response()->json([
            'result' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit(int $id)
    {
        $data = Props::find($id);
        $category = PropsCategory::whereNull('deleted_at')->get();
        return view('backstage.props.edit')->with(['data' => $data, 'categories'=>$category]);
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
        $params = $request->all();
        $param  = $request->except('_token');

        if (isset($params['is_delete']) && in_array($params['is_delete'], ['on', 'off'])) {
            $param['is_delete']  = $params['is_delete']=='off' ? 1 : 0;
            $param['deleted_at'] = $params['is_delete']=='off' ? date('Y-m-d H:i:s') : null;
        } elseif (isset($params['recommendation']) && in_array($params['recommendation'], ['on', 'off'])) {
            $param['recommendation'] = $params['recommendation']=='on' ? 1 : 0;
        } elseif (isset($params['default']) && in_array($params['default'], ['on', 'off'])) {
            $param['default'] = $params['default']=='on' ? 1 : 0;
        } else {
            $this->validate($request, [
                'name'  => 'required|string',
                'hash'  => 'required|string',
                'cover' => 'required|string',
                'url'   => 'required|string',
            ]);
        }
        $param['updated_at'] = date('Y-m-d H:i:s');

        if (!empty($param['hash'])) {
            $param['hash'] = strtolower($param['hash']);
        }

        Props::where('id', $id)->update($param);
        return response()->json(['result' => 'success']);
    }

}
