<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/25
 * Time: 17:06
 */

namespace App\Http\Controllers\Content;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MusicController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $limit = intval($request->input('limit' , 10));
        if($request->ajax())
        {
            return DB::connection('lovbee')->table('bgms')->paginate($limit);
        }
        return view('backstage.content.music.index');
    }

    public function update(Request $request , $id)
    {
        $data = array();
        if($request->has('is_delete'))
        {
            $is_delete = $request->input('is_delete' , 'on');
            $data['is_delete'] = $is_delete=='on'?1:0;
        }
        if($request->has('recommendation'))
        {
            $recommendation = $request->input('recommendation' , 'on');
            $data['recommendation'] = $recommendation=='on'?1:0;
        }
        if($request->has('time'))
        {
            $data['time'] = intval($request->input('time' , 15));
        }
        if($request->has('name'))
        {
            $name = strval($request->input('name' , ''));
            !blank($name)&&$data['name'] = $name;
        }
        if(!blank($data))
        {
            DB::connection('lovbee')->table('bgms')->where('id' , $id)->update($data);
        }
        return response()->json([
            'result' => 'success',
        ]);

    }

}