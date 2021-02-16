<?php

namespace App\Http\Controllers\Content;

use App\Models\Content\Topic;
use App\Repositories\Contracts\Content\TopicRepository;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Content\PostTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class TopicController extends Controller
{

    private $topic;

    public function __construct(TopicRepository $topic)
    {
        $this->topic = $topic;
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

        $result = $this->topic->findByWhere($params);
        $params['appends'] = $params;
        $params['data'] = $result;

        return view('backstage.content.topic.index' , $params);
    }

    public function add()
    {
        return view('backstage.content.topic.create');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backstage.content.topic.add', ['data'=>null]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'topic_content' => 'required|string',
            'flag'          => 'required|int',
            'sort'          => 'required|int',
            'start_time'    => 'required|string',
            'end_time'      => 'required|string',
        ]);
        $fields = $request->only(['id', 'topic_content', 'flag', 'sort', 'start_time', 'end_time']); // except
        if(empty($fields['id'])) unset($fields['id']);
        $fields['start_time'] = $fields['start_time'] ? strtotime($fields['start_time']) : 0;
        $fields['end_time']   = $fields['end_time']   ? strtotime($fields['end_time'])   : 0;
        $this->topic->store($fields);
        $this->setHotTopic();
        return response()->json([
            'result' => 'success',
        ]);
    }

    /**
     * 设置热搜缓存
     */
    /*protected function setHotTopic()
    {
        return true;
        $key    = 'hot_topic';
        $topics = $this->topic->getList(20);
        if(!empty($topics)) {
            Redis::set($key, json_encode($topics, JSON_UNESCAPED_UNICODE));
        }
    }*/

    public function setHotTopic()
    {
        $time_stamp = time();
        $referer = 'backstage';
        $query = [
            'referer'=>$referer,
            'time_stamp'=>$time_stamp,
        ];
        $client = new Client();
        $signature = common_signature($query);
        $query['signature'] = $signature;
        $client->request('POST', front_url('api/bk/set/topic/hot') , array('query'=>$query));

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        var_dump('123');die;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->topic->find($id);
        return view('backstage.content.topic.edit')->with(['data' => $data]);
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
            'topic_content' => 'required|string',
            'flag'          => 'required|int',
            'sort'          => 'required|int',
            'start_time'    => 'required|string',
            'end_time'      => 'required|string',
        ]);
        $data  = $this->topic->find($id);
        $param = $request->all();
        $param['start_time'] = $param['start_time'] ? strtotime($param['start_time']) : 0;
        $param['end_time']   = $param['end_time']   ? strtotime($param['end_time'])   : 0;

        $this->topic->update($data, $param);
        $this->setHotTopic();
        return response()->json(['result' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function destroy($id)
    {
        $post = $this->topic->find($id);
        if(!empty($post)) {
            $post_uuid = $post->post_uuid;
            $time = time();
            $client = new Client();
            $params = array(
                'post_uuid'=>$post_uuid,
                'time_stamp'=>$time
            );
            $signature = common_signature($params);
            $params['signature'] = $signature;
            $url = front_url('api/bk/post/');
            $data = [
                'form_params' => $params
            ];
            try {
                $response = $client->request('DELETE', $url.$post_uuid, $data);
                $code = $response->getStatusCode();
                if($code!=204)
                {
                    abort($code);
                }
            } catch (GuzzleException $e) {
                abort($e->getCode() , $e->getMessage());
            }

//            if(empty($post->post_deleted_at))
//            {
//                $this->topic->destroy($post);
//            }else{
//                $this->topic->restore($post);
//            }
        }
        return response()->json([
            'result' => 'success',
        ]);
    }

    public function clearCache()
    {
        $client = new Client();
        $client->request('GET', 'https://api.yooul.net/api/clear/cache');
        return response()->json([
            'result' => 'success',
        ]);

    }
}
