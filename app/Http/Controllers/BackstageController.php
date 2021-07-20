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
     * @throws \Throwable
     */
    public function index()
    {
        return view('backstage.index');
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * 获取热门话题
     * @throws \Throwable
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
