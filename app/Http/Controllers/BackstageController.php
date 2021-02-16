<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Repositories\Contracts\ConfigRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class BackstageController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        dd(auth()->user()->getAllPermissions()->toArray());

//        echo 1;die;
//        $t = Translation::first();
//
//    // Solution 2 : Mass assignement if you have multiple fields to be saved.
//    //$category->translate('en')->fill(array());
//
//        dd($t->value);

        //echo '<h1 style="color: red;">已登录</h1>';
        return view('backstage.index');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * 获取热门话题
     */
    public function getHotTopic()
    {
        $topics = [
            ['topic_content' => 'title1', 'sort'  => 3,'flag' => 1],
            ['topic_content' => 'title2', 'sort'  => 1,'flag' => 1],
            ['topic_content' => 'title0', 'sort'  => 2,'flag' => 1],
            ['topic_content' => 'title5', 'sort'  => 4,'flag' => 2],
            ['topic_content' => 'title6', 'sort'  => 6,'flag' => 2],
            ['topic_content' => 'title4', 'sort'  => 5,'flag' => 2],
        ];


        $topics = $this->sortArrByManyField($topics, 'flag', SORT_ASC, 'sort', SORT_DESC);


        return view('backstage.topic.index' , compact('topics'));


        $query = [
            'referer'   => 'backstage',
            'time_stamp'=> time(),
        ];
        $signature          = common_signature($query);
        $query['signature'] = $signature;
        $client  = new Client();
        $client->request('GET', 'http://tsm.api.mmantou.cn/api/bk/get/topic/hot' , array('query'=>$query));
        dump($client);
    }


    public function sortArrByManyField()
    {
        $args = func_get_args(); // 获取函数的参数的数组
        if (empty($args)) {
            return null;
        }
        $arr = array_shift($args);
        if (!is_array($arr)) {
            return $arr;
        }
        foreach ($args as $key => $field) {
            if (is_string($field)) {
                $temp = array();
                foreach ($arr as $index => $val) {
                    $temp[$index] = $val[$field];
                }
                $args[$key] = $temp;
            }
        }
        $args[] = &$arr; //引用值
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

}
