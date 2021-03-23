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
        $this->validate($request, [
            'title_en' => 'required|string',
            'en'    => 'required|string',
        ]);
        $title           = ['en'=>$params['title_en'], 'zh-CN'=>$params['title_cn']];
        $content         = ['en'=>$params['en'], 'zh-CN'=>$params['zh-CN']];
        $data['title']   = json_encode($title   ?? [], JSON_UNESCAPED_UNICODE);
        $data['content'] = json_encode($content ?? [], JSON_UNESCAPED_UNICODE);
        $data['sort']    = !empty($params['sort'])   ? $params['sort']   : 0;
        $data['status']  = !empty($params['status']) ? $params['status'] : 0;
        $data['created_at'] = date('Y-m-d H:i:s');

        Question::create($data);
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
            $title   = ['en'=>$params['title_en'], 'zh-CN'=>$params['title_cn']];
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
            $html .= "<title>{$title[$key]}</title><style>html,body,div,span,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,a,address,em,img,ol,ul,li,fieldset,form,label,legend,table,tbody,tfoot,thead,tr,th,td,i,s{margin:0;padding:0;border:0;font-weight:inherit;font-style:inherit;font-size:100%;font-family:Helvetica,Arial,sans-serif}ul,ol{list-style:none}a img{border:0;vertical-align:top}a{text-decoration:none}button{overflow:visible;padding:0;margin:0;border:0 none;background-color:transparent}button::-moz-focus-inner{padding:0}textarea,input{background:0;padding:0;-webkit-border-radius:0;-moz-border-radius:0;border-radius:0;-webkit-appearance:none}textarea:focus,input:focus,button:focus{outline:0}body{--BG-0:#ededed;--BG-1:#f7f7f7;--BG-2:#fff;--BG-3:#f7f7f7;--BG-4:#4c4c4c;--BG-5:#fff;--FG-0:rgba(0,0,0,0.9);--FG-HALF:rgba(0,0,0,0.9);--FG-1:rgba(0,0,0,0.5);--FG-2:rgba(0,0,0,0.3);--FG-3:rgba(0,0,0,0.1);--RED:#fa5151;--ORANGE:#fa9d3b;--YELLOW:#ffc300;--GREEN:#91d300;--LIGHTGREEN:#95ec69;--BRAND:#07c160;--BLUE:#10aeff;--INDIGO:#1485ee;--PURPLE:#6467f0;--LINK:#576b95;--TEXTGREEN:#06ae56;--FG:black;--BG:white;--BTN-DEFAULT-COLOR:#06ae56;--BTN-DISABLED-FONT-COLOR:rgba(0,0,0,0.2);--BTN-DEFAULT-BG:#f2f2f2;word-break:break-word;word-wrap:break-word;-webkit-user-select:none;user-select:none;-webkit-text-size-adjust:none}*{-webkit-tap-highlight-color:rgba(0,0,0,0)}img,canvas,iframe,svg{max-width:100%}.overflow-container{overflow-y:scroll}.container .title:before{content:\"\";position:absolute;bottom:0;left:15px;right:15px;border-bottom:1px solid #dfdfdf;-webkit-transform:scaleY(0.5);-ms-transform:scaleY(0.5);transform:scaleY(0.5);-webkit-transform-origin:0 0;-ms-transform-origin:0 0;transform-origin:0 0;-webkit-box-sizing:border-box;box-sizing:border-box;border-color:var(--FG-3)!important}.title{margin-bottom:15px!important;color:var(--FG-0)!important;position:relative;padding:17px 15px;font-size:18px;line-height:24px;font-weight:bolder}.content{font-size:16px;line-height:24px;padding:12px 15px 0;text-align:justify;color:var(--FG-0)!important}</style></head><body ontouchstart=''>";
            $html .= "<div id='container' class='container'><h2 class='title'>{$title[$key]}</h2><div class='content'>";
            $html .= $item;
            $html .= '</div></div></div></body>';

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