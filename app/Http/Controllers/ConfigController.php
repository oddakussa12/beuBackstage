<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreConfigRequest;
use App\Repositories\Contracts\ConfigRepository;

class ConfigController extends Controller
{
    public function __construct(ConfigRepository $config)
    {
        $this->config = $config;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $custom_config = $this->config->all();
        foreach ($custom_config as $v)
        {
            config([$v['config_key']=>$v['config_value']]);
        }
        $post_rate = $this->getPostRate();
        $dx        = $this->getDX();

        return view('backstage.config.index' , compact('post_rate' , 'dx'));
    }

    public function getPostRate(float $rate = 0)
    {
        if($rate>0)
        {
            Cache::forget('post_rate');
        }
        return Cache::rememberForever('post_rate' , function() use ($rate){
            return $rate;
        });
    }

    public function setDx($android , $ios ,$post_uuid)
    {
        $time_stamp = time();
        $referer = 'backstage';
        $params = [
            'referer'=>$referer,
            'time_stamp'=>$time_stamp,
            'post_uuid'=>$post_uuid,
        ];

        $ios_data = [
            'form_params' => array_merge($params , array('type'=>'ios' , 'switch'=>$ios))
        ];
        $android_data = [
            'form_params' => array_merge($params , array('type'=>'android' , 'switch'=>$android))
        ];

        $url = front_url();
        $client = new Client(['base_uri' => $url]);
        $promises = [
            'ios' => $client->postAsync('api/set/dx/clearDxCache' , $ios_data),
            'android'   => $client->postAsync('api/set/dx/clearDxCache' , $android_data),
        ];
        $results = Promise\unwrap($promises);

        $ios_res = \json_decode($results['ios']->getBody()->getContents(),true);

        $android_res = \json_decode($results['android']->getBody()->getContents(),true);



//        $client = new Client();
//        $signature = common_signature($query);
//        $query['signature'] = $signature;
//        $client->request('POST', 'https://api.yooul.net/api/set/dx/clearDxCache' , $data);
        $this->getDX($post_uuid,$android,$ios  , true);
        return true;
    }

    public function getDX($post_uuid='',$android=0 , $ios=0 , $set=false)
    {
        if((bool)$set)
        {
            Cache::forget('dx');
        }
        return Cache::rememberForever('dx' , function() use ($android,$ios,$post_uuid){
            return array('android'=>$android , 'ios'=>$ios , 'post_uuid'=>$post_uuid);
        });
    }


    public function setHotSearch($titles)
    {
        if(!empty($titles))
        {
            $time_stamp = time();
            $referer = 'backstage';
            $query = [
                'referer'=>$referer,
                'time_stamp'=>$time_stamp,
                'titles'=>\json_encode($titles)
            ];
            $client = new Client();
            $signature = common_signature($query);
            $query['signature'] = $signature;
            $client->request('POST', front_url('/api/bk/set/search/hot'), array('query'=>$query));
        }
        return true;
    }

    public function setIndexSwitch($index_switch)
    {
        $time_stamp = time();
        $referer = 'backstage';
        $query = [
            'referer'=>$referer,
            'time_stamp'=>$time_stamp,
            'index_switch'=>$index_switch
        ];
        $client = new Client();
        $signature = common_signature($query);
        $query['signature'] = $signature;
        $client->request('POST', front_url('/api/bk/set/index/switch'), array('query'=>$query));
        return true;
    }

    public function setFakeLikeCoefficient($fake_like_coefficient)
    {
        $time_stamp = time();
        $referer = 'backstage';
        $query = [
            'referer'=>$referer,
            'time_stamp'=>$time_stamp,
            'fake_like_coefficient'=>$fake_like_coefficient
        ];
        $client = new Client();
        $signature = common_signature($query);
        $query['signature'] = $signature;
        $client->request('POST', front_url('/api/bk/set/fake/like/coefficient'), array('query'=>$query));
        return true;
    }

    public function setPostGravity($post_gravity)
    {
        $time_stamp = time();
        $referer = 'backstage';
        $query = [
            'referer'=>$referer,
            'time_stamp'=>$time_stamp,
            'post_gravity'=>$post_gravity
        ];
        $client = new Client();
        $signature = common_signature($query);
        $query['signature'] = $signature;
        $client->request('POST', front_url('/api/bk/set/post/gravity'), array('query'=>$query));
        return true;
    }

    public function setPreheatPostCommentCount($c)
    {
        $time_stamp = time();
        $referer = 'backstage';
        $query = [
            'referer'=>$referer,
            'time_stamp'=>$time_stamp,
            'post_init_comment_num'=>$c
        ];
        $client = new Client();
        $signature = common_signature($query);
        $query['signature'] = $signature;
        $client->request('post', front_url('/api/bk/set/user/post/preheat/coefficient'), array('query'=>$query));
        return true;
    }

    public function setKolUserX($x)
    {
        $time_stamp = time();
        $referer = 'backstage';
        $query = [
            'referer'=>$referer,
            'time_stamp'=>$time_stamp,
            'user_kol_x'=>$x
        ];
        $client = new Client();
        $signature = common_signature($query);
        $query['signature'] = $signature;
        $client->request('post', front_url('/api/bk/set/user/post/preheat/coefficient'), array('query'=>$query));
        return true;
    }

    public function setPostRate($rate)
    {
        $time_stamp = time();
        $referer = 'backstage';
        $query = [
            'referer'=>$referer,
            'time_stamp'=>$time_stamp,
            'rate'=>$rate
        ];
        $client = new Client();
        $signature = common_signature($query);
        $query['signature'] = $signature;
        $client->request('GET', front_url('/api/set/post/rate'), array('query'=>$query));
        return true;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreConfigRequest $request)
    {
        $type = $request->input('config_type' , '');
        if($type=='dx'){
            $android = $request->input('android');
            $ios = $request->input('ios');
            $android = $android=='on'?1:0;
            $ios = $ios=='on'?1:0;
            $post_uuid = $request->input('post_uuid');
            $this->setDx($android, $ios , $post_uuid);
        }elseif($type=='hot_search'){
            $titles = $request->input('hot_search' , '');
            if(strpos($titles , ',')===false)
            {
                $titles = explode("\n" , $titles);
            }else{
                $titles = explode("," , $titles);
            }
            $titles = array_filter($titles , function($v,$i){
                return !empty($v);
            } , ARRAY_FILTER_USE_BOTH);

            $titles = array_map(function($v){
                $v = str_replace("\r\n" , "" , $v);
                $v = str_replace("\r" , "" , $v);
                $v = str_replace("\n" , "" , $v);
                $v = str_replace(' ' , "" , $v);
                return $v;
            } , $titles);
            $this->setHotSearch($titles);
        }else{
            $params = $request->all();
            unset($params['config_type']);
            $param = array_dot($params);
            if($type=='post_rate')
            {
                $post_rate = array_shift($param);
                $this->setPostRate($post_rate);
            }elseif($type=='post_gravity')
            {
                $post_gravity = array_shift($param);
                $this->setPostGravity($post_gravity);
            }elseif ($type=='fake_like_coefficient')
            {
                $fake_like_coefficient = array_shift($param);
                $this->setFakeLikeCoefficient($fake_like_coefficient);
            }elseif ($type=='index_switch')
            {
                $index_switch = array_shift($param);
                $index_switch = $index_switch=='on'?1:0;
                $this->setIndexSwitch($index_switch);
            }elseif ($type=='post_init_comment_num')
            {
                $post_init_comment_num = array_shift($param);
                $post_init_comment_num = intval($post_init_comment_num);
                $this->setPreheatPostCommentCount($post_init_comment_num);
            }elseif ($type=='user_kol_x')
            {
                $user_kol_x = array_shift($param);
                $user_kol_x = intval($user_kol_x);
                $this->setKolUserX($user_kol_x);
            }
            $this->config->createOrUpdate(array_dot($params));
        }
        return response()->json([
            'result' => 'success',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}