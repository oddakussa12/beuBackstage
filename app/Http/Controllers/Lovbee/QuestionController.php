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
use Qiniu\Storage\FormUploader;
use Qiniu\Storage\UploadManager;
use function Sodium\compare;

class QuestionController extends Controller
{

    private $language = ['en', 'zh-CN'];
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
        foreach ($result as $item) {
            $item['content'] = json_decode($item['content'], true);
            $item['url']     = json_decode($item['url'], true);
        }
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
        return view('backstage.lovbee.question.create', ['languages'=>['en', 'zh-CN']]);
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
        return view('backstage.lovbee.question.edit', ['data' => $data, 'languages'=>['en', 'zh-CN']]);
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
        $params   = $request->all();
        $question = Question::find($id);
        if ($request->has('title')) {
            $this->validate($request, [
                'title' => 'required|string',
                'en'    => 'required|string',
            ]);
            $content = ['en'=>$params['en'], 'zh-CN'=>$params['zh-CN']];
        }
        $question->content =  empty($content)          ? $question->content   : json_encode($content ?? [], JSON_UNESCAPED_UNICODE);
        $question->title   = !empty($params['title'])  ? $params['title']     : $question->title;
        $question->sort    = !empty($params['sort'])   ? (int)$params['sort'] : $question->sort;
        $question->status  = !empty($params['status']) ? $params['status']    : $question->status;
        $question->save();

        return response()->json(['result' => 'success']);
    }

    public function upload($id)
    {
        $question = Question::find($id);

        $title = [
            'en' => '常见问题',
            'zh-CN' => '常见问题',
        ];
        $header = [
            'en' => '请选择问题发生的场景',
            'zh-CN' => '请选择问题发生的场景',
        ];

        $content = json_decode($question['content'], true);

        $url = [];
        foreach ($content as $key=>$item) {
            $fileName = $key.$question['id'].'.html';
            $html  = '<!DOCTYPE html><html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">';
            $html .= "<title>{$title[$key]}</title><style> body { word-break:break-word;}</style><body ontouchstart=''><div id='container' class='container'><div>{$header[$key]}</div>";
            $html .= $item;
            $html .= '</div></body>';
            file_put_contents($fileName, $html);
            $result = $this->uploadQiNiu($fileName);
            if (!empty($result) && !empty($result['name'])) {
                $url[$key] = $result['url'].$result['name'];
            }
        }
        if (!empty($url)) {
            $question->url = json_encode($url);
            $question->save();
        }

        return response()->json(['result', 'success']);
    }

    public function uploadQiNiu($filename)
    {
        $qn_token  = qnToken('qn_event_source');
        $file      = realpath($filename);
        $upManager = new UploadManager();
        list($ret, $error) = $upManager->putFile($qn_token['token'], 'formPutFile', $file, null, 'text/plain', null);
        return $ret;
    }
}