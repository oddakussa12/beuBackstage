<?php

namespace App\Http\Controllers\Props;

use App\Models\Props\Props;
use Illuminate\Http\Request;
use App\Models\Props\PropsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class PropsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $params = $request->all();
        $params['appends'] = $params;
        $props  = new Props();

        if (!empty($params['sort'])) {
            $sort  = $params['sort'] == 'desc' ? 'desc' : 'asc';
            $props = $props->orderBy('sort', $sort);
        } else {
            $props = $props->orderByDesc('id');
        }
        if (!empty($params['name'])) {
            $props = $props->where('name', 'like', "%{$params['name']}%");
        }
        if (!empty($params['category'])) {
            $props = $props->where('category', $params['category']);
        }
        if (isset($params['recommend'])) {
            $props = $props->where('recommendation', $params['recommend']);
        }
        if (isset($params['hot'])) {
            $props = $props->where('hot', $params['hot']);
        }

        $props = $props->paginate(10);
        $params['data']    = $props;
        $params['categories'] = PropsCategory::where('is_delete', 0)->get();

        return view('backstage.props.props.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     * @throws \Throwable
     */
    public function create()
    {
        $category = PropsCategory::where('is_delete', 0)->get();
        return view('backstage.props.props.create', ['data' => null, 'categories'=>$category]);
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
        $this->validate($request, [
            'name'  => [
                'required',
                'alpha',
                'max:30',
                Rule::unique('lovbee.props')
            ],
            'cover' => 'required|string|url',
            'url'   => 'required|string|url',
            'hash'  => [
                'required',
                'alpha_num',
                'size:32',
                Rule::unique('lovbee.props')
            ],
        ]);

        if (!empty($params['hash'])) {
            $params['hash'] = strtolower($params['hash']);
        }
        Props::create($params);
        return response()->json([
            'result' => 'success',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * @throws \Throwable
     */
    public function edit(int $id)
    {
        $data = Props::find($id);
        $category = PropsCategory::where('is_delete', 0)->get();
        return view('backstage.props.props.edit')->with(['data' => $data, 'categories'=>$category]);
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
        $data  = $request->except(['_token' , 'id']);
        if(isset($params['id']))
        {
            $this->validate($request, [
                'name'  => [
                    'required',
                    'alpha',
                    'max:30',
                    Rule::unique('lovbee.props')->ignore($id, 'id'),
                ],
                'hash'  => [
                    'required',
                    'alpha_num',
                    'size:32',
                    Rule::unique('lovbee.props')->ignore($id, 'id'),
                ],
                'cover' => 'required|string|url',
                'url'   => 'required|string|url',
            ]);
            $data['hash'] = strtolower($data['hash']);
        }else{
            if (isset($params['hot']) && in_array($params['hot'], ['on', 'off'])) {
                $data['hot']  = $params['hot']=='on' ? 1 : 0;
            } elseif (isset($params['is_delete']) && in_array($params['is_delete'], ['on', 'off'])) {
                $data['is_delete']  = $params['is_delete']=='off' ? 1 : 0;
                $data['deleted_at'] = $params['is_delete']=='off' ? date('Y-m-d H:i:s') : null;
            } elseif (isset($params['recommendation']) && in_array($params['recommendation'], ['on', 'off'])) {
                $data['recommendation'] = $params['recommendation']=='on' ? 1 : 0;
            } elseif (isset($params['sort'])) {
                $data['sort'] = intval($params['sort']);
            }
        }
        if(!blank($data))
        {
            $data['updated_at'] = date('Y-m-d H:i:s');
            Props::where('id', $id)->update($data);
        }
        return response()->json(['result' => 'success']);
    }

}
