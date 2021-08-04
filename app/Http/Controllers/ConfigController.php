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
    private $config;

    public function __construct(ConfigRepository $config)
    {
        $this->config = $config;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function index()
    {
        $custom_config = $this->config->all();
        foreach ($custom_config as $v)
        {
            config([$v['config_key']=>$v['config_value']]);
        }
        return view('backstage.config.index');
    }

    public function store(Request $request)
    {
        $params = $request->all();
        $fields = collect($params)->reject(function($field , $key){
            return !is_array($key)&&!is_array($field);
        })->toArray();
        $fields = array_dot($fields);
        $fields = collect($fields)->reject(function ($field , $key){
            return blank($field)||empty($key);
        })->toArray();
        !empty($fields)&&$this->config->createOrUpdate($fields);
        if(isset($params['remote']))
        {
            $remote = \json_decode($params['remote'] , true);
            is_array($remote)&&$this->httpRequest($remote['url'] , $remote['params'] , $remote['method']);
        }
        return response()->json(array(
            'result'=>'success'
        ));
    }
}