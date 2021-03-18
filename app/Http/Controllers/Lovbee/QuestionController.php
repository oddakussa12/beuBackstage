<?php
namespace App\Http\Controllers\Lovbee;

use App\Exports\MessageExport;
use App\Exports\UsersExport;
use App\Models\Props\PropsCategory;
use App\Models\Question;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use function Sodium\compare;

class QuestionController extends Controller
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
        $result = Question::orderByDesc('id')->paginate(10);
        $params['appends'] = $params;
        $params['data']    = $result;
        return view('backstage.lovbee.question.index', $params);
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
        return view('backstage.lovbee.question.create', ['languages'=>['en', 'zh-CN'], 'categories'=>$category]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $params  = $request->except('_token');
        $content = ['en'=>$params['en'], 'zh-CN'=>$params['zh-CN']];
        $params['content'] = json_encode($content ?? [], JSON_UNESCAPED_UNICODE);
        $params['created_at'] = date('Y-m-d H:i:s');
        unset($params['en'], $params['zh-CN']);
        Question::create($params);
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
        $data = Question::find($id);
        if (!empty($data)) {
            $data['content'] = json_decode($data['content'], true);
        }
        $category = PropsCategory::where('is_delete', 0)->get();
        return view('backstage.lovbee.question.edit', ['data' => $data, 'languages'=>['en', 'zh-CN'], 'categories'=>$category]);
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
        $question = Question::find($id);
        if ($request->has('title')) {
            $this->validate($request, [
                'title'    => ['required', 'string'],
                'category' => 'required|array',
                'language' => ['required', 'array',
                    function ($value, $fail){
                        if(!in_array('en', $value)) {
                            $fail('English title is missing');
                        }
                    }
                ]
            ]);
            $params = $request->all();

            $languages = (array)$request->input('language');
            $category  = (array)$request->input('category');
            foreach ($languages as $key=>$language) {
                $ext[$language] = $category[$key];
            }
        }
        $question->content =  empty($ext)              ? $question->content : json_encode($ext ?? [], JSON_UNESCAPED_UNICODE);
        $question->title   = !empty($params['title'])  ? $params['title']     : $question->title;
        $question->url     = !empty($params['url'])    ? $params['url']       : $question->url;
        $question->sort    = !empty($params['sort'])   ? $params['sort']      : $question->sort;
        $question->status  = !empty($params['status']) ? $params['status']    : $question->status;


        return response()->json(['result' => 'success']);
    }
}