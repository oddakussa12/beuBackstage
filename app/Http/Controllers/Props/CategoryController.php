<?php

namespace App\Http\Controllers\Props;

use App\Http\Controllers\Controller;
use App\Models\PropsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
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
        $goods   = PropsCategory::orderByDesc('id')->paginate(10);
        $params['appends'] = $params;
        $params['data']    = $goods;

        return view('backstage.category.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        return view('backstage.category.create', ['data' => null, 'counties'=>config('country')]);
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
            'name'  => 'required|string',
        ]);
        $params = $request->except('_token');
        $result = PropsCategory::where('name', $params['name'])->first();

        if ($result) {
            return ['code'=>200, 'result'=>'name alreadly existed'];
        }

        PropsCategory::create($params);
        return response()->json(['result'=>'success']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit($id)
    {
        $data = PropsCategory::find($id);
        return view('backstage.category.edit')->with(['data' => $data, 'counties'=>config('country')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $params = $request->all();
        $param  = $request->except('_token');

        $param['updated_at'] = date('Y-m-d H:i:s');

        PropsCategory::where('id', $id)->update($param);
        return response()->json(['result' => 'success']);
    }

}
