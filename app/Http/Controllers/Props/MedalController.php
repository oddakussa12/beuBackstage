<?php

namespace App\Http\Controllers\Props;

use App\Models\Medal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Props\PropsCategory;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class MedalController extends Controller
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
        $data   = Medal::orderByDesc('id')->paginate(10);
        foreach ($data as $item) {
            $item['name'] = json_decode($item['name'], true);
            $item['desc'] = json_decode($item['desc'], true);
        }
        $params['appends'] = $params;
        $params['data']    = $data;

        return view('backstage.props.medal.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     * @throws \Throwable
     */
    public function create()
    {
        return view('backstage.props.medal.create', ['data' => null, 'countries'=>config('country')]);
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
        $data = Medal::find($id);
        if (!empty($data)) {
            $data['name'] = json_decode($data['name'], true);
            $data['desc'] = json_decode($data['desc'], true);
        }

        return view('backstage.props.medal.edit')->with(['data' => $data, 'category'=>['once'=>'一次性成就', 'aggregate'=>'累计成就', 'great'=>'Great']]);
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
        $data = Medal::find($id);
        $sort = $request->input('sort');

        if (isset($sort)) {
            $data->sort = !empty($sort) ? (int)$sort : $data->sort;
        } else {
            $this->validate($request, [
                'name_en'  => 'required|string',
                'desc_en'  => 'required|string',
                'category' => 'required|string',
            ]);
            $name_en  = $request->input('name_en');
            $name_cn  = $request->input('name_cn');
            $desc_en  = $request->input('desc_en');
            $desc_cn  = $request->input('desc_cn');
            $category = $request->input('category');
            $score    = $request->input('score');
            $image    = $request->input('image');
            $name     = ['cn'=>$name_cn, 'en'=>$name_en];
            $desc     = ['cn'=>$desc_cn, 'en'=>$desc_en];
            $data->name     = json_encode($name, JSON_UNESCAPED_UNICODE);
            $data->desc     = json_encode($desc, JSON_UNESCAPED_UNICODE);
            $data->category = !empty($category) ? $category : $data->category;
            $data->score    = !empty($score)    ? $score    : $data->score;
            $data->image    = !empty($image)    ? $image    : $data->image;
        }

        $data->save();

        return response()->json(['result' => 'success']);
    }
}