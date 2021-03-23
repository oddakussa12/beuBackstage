<?php
namespace App\Http\Controllers\Service;

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
            $item['title']   = json_decode($item['title'], true);
            $item['content'] = json_decode($item['content'], true);
            $item['url']     = json_decode($item['url'], true);
        }
        $params['appends'] = $params;
        $params['data']    = $result;
        return view('backstage.service.question.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\View\View
     * @throws \Throwable
     */
    public function create()
    {
        return view('backstage.lovbee.question.create', ['languages'=>$this->language]);
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

        foreach ($this->language as $item) {
            unset($params[$item]);
        }

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
            $data['title']   = json_decode($data['title'], true);
        }
        return view('backstage.service.question.edit', ['data' => $data, 'languages'=>$this->language]);
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
        if ($request->has('title_en')) {
            $this->validate($request, [
                'title_en' => 'required|string',
                'en'    => 'required|string',
            ]);
            $title   = ['en'=>$params['title_en'], 'zh-CN'=>$params['title_zh-CN']];
            $content = ['en'=>$params['en'], 'zh-CN'=>$params['zh-CN']];
        }
        $question->content =  empty($content)          ? $question->content   : json_encode($content ?? [], JSON_UNESCAPED_UNICODE);
        $question->title   =  empty($title)            ? $question->title     : json_encode($title   ?? [], JSON_UNESCAPED_UNICODE);
        $question->sort    = !empty($params['sort'])   ? (int)$params['sort'] : $question->sort;
        $question->status  = !empty($params['status']) ? $params['status']    : $question->status;
        $question->save();

        return response()->json(['result' => 'success']);
    }

    public function upload($id)
    {
        $question = Question::find($id);

        /*foreach ($this->language as $item) {
            $title[$item]  = $item=='en' ? 'Help Center' : '常见问题';
            $header[$item] = $item=='en' ? 'Popular Topics' : '请选择问题发生的场景';
        }*/

        $title   = json_decode($question['title'], true);
        $content = json_decode($question['content'], true);

        $url = [];
        foreach ($content as $key=>$item) {
            $fileName = $key.$question['id'].'.html';
            $html  = '<!DOCTYPE html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">';
            $html .= "<title>{$title[$key]}</title><style> body { word-break:break-word;margin-top: 16px;margin-bottom: 3px;padding-left: 16px;padding-right: 16px;}.title{color: rgba(0,0,0,.5);font-size: 14px;line-height: 1.4;}</style></head><body ontouchstart=''>";
            $html .= "<div id='container' class='container'><div class='title'>{$title[$key]}</div><div class='content'></div>";
            $html .= $item;
            $html .= '</div></div></body>';

            file_put_contents($fileName, ($html));
            $result = $this->uploadQiNiu($fileName);

            if (!empty($result) && !empty($result['name'])) {
                $url[$key] = $result['url'].$result['name'];
            }
            unlink($fileName);
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
        try {
            list($ret, $error) = $upManager->putFile($qn_token['token'], 'formPutFile', $file);
        } catch (\Exception $e) {
            Log::error('Upload File Exception:'. $e->getMessage());
        }
        return $ret ?? [];
    }
}