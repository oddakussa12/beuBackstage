<?php

namespace App\Http\Controllers\Props;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Props\PropsCategory;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
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
        $goods  = PropsCategory::orderByDesc('id')->paginate(10);
        $params['appends'] = $params;
        $params['data']    = $goods;

        return view('backstage.props.category.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     * @throws \Throwable
     */
    public function create()
    {
        return view('backstage.props.category.create', ['data' => null, 'counties'=>config('country')]);
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
            'name'      => [
                'required',
                'alpha',
                'max:30',
                'unique:lovbee.props_categories,name',
            ],
            'category'  => 'required|array',
            'language'  => [
                'required',
                'array',
                function ($attribute, $value, $fail){
                    if(!in_array('en', $value))
                    {
                        $fail('English name is missing');
                    }
                }
            ]
        ]);
        foreach ($params['language'] as $key=>$language) {
            $ext[$language] = $params['category'][$key];
        }
        $data['language'] = json_encode($ext ?? [], JSON_UNESCAPED_UNICODE);
        $data['name']     = $params['name'];
        PropsCategory::create($data);
        return response()->json(['result'=>'success']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * @throws \Throwable
     */
    public function edit($id)
    {
        $data = PropsCategory::find($id);
        if (!empty($data)) {
            $data['language'] = json_decode($data['language'], true);
        }
        return view('backstage.props.category.edit')->with(['data' => $data, 'counties'=>config('country')]);
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
        $data = array();
        if($request->has('is_delete'))
        {
            $is_delete = $request->input('is_delete' , 'on');
            $data['is_delete'] = $is_delete=='on'?1:0;
        }
        if($request->has('sort'))
        {
            $data['sort'] = intval($request->input('sort' , 0));
        }
        if($request->has('name'))
        {
            $this->validate($request, [
                'name'      => [
                    'required',
                    'alpha',
                    'max:30',
                    Rule::unique('lovbee.props_categories')->ignore($id, 'id'),
                ],
                'category'  => 'required|array',
                'language'  => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail){
                        if(!in_array('en', $value))
                        {
                            $fail('English name is missing');
                        }
                    }
                ]
            ]);
            $languages = (array)$request->input('language');
            $category = (array)$request->input('category');
            foreach ($languages as $key=>$language) {
                $ext[$language] = $category[$key];
            }
            $data['language']   = json_encode($ext ?? [], JSON_UNESCAPED_UNICODE);
            $data['name']       = $request->input('name');
        }
        if(!blank($data))
        {
            $data['updated_at'] = Carbon::now()->toDateTimeString();
            PropsCategory::where('id', $id)->update($data);
        }
        !blank($data)&&PropsCategory::where('id', $id)->update($data);
        return response()->json(['result' => 'success']);
    }

    /**
     * @param $word
     * @return string|string[]|force(kmixed)|null
     * 过滤英文标点
     */
    public function filter($word)
    {
        $word = str_replace('&amp;', '', $word); // &
        $word = str_replace(' ', '', $word);
        // Filter 英文标点符号
        return preg_replace("/[[:punct:]]/i","",$word);


    }
}
