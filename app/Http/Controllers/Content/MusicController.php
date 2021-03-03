<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 2019/5/25
 * Time: 17:06
 */

namespace App\Http\Controllers\Content;


use Carbon\Carbon;
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
        if($request->has('status'))
        {
            $status = $request->input('status' , 'on');
            $data['status'] = $status=='on'?1:0;
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
            $data['updated_at'] = Carbon::now()->toDateTimeString();
            DB::connection('lovbee')->table('bgms')->where('id' , $id)->update($data);
        }
        return response()->json([
            'result' => 'success',
        ]);

    }


    public function store(Request $request)
    {
        $data = array();
        $this->validate($request, [
            'name' => 'required|alpha_num',
            'music' => 'required|string',
            'hash' => 'required|string',
            'time' => 'required|string',
        ]);
        if($request->has('name'))
        {
            $name = strval($request->input('name' , ''));
            !blank($name)&&$data['name'] = $name;
        }
        if($request->has('music'))
        {
            $url = strval($request->input('music' , ''));
            !blank($url)&&$data['url'] = $url;
        }
        if($request->has('hash'))
        {
            $hash = strval($request->input('hash' , ''));
            !blank($hash)&&$data['hash'] = $hash;
        }
        if($request->has('time'))
        {
            $data['time'] = intval($request->input('time' , 15));
        }
        if(!blank($data))
        {
            $data['created_at'] = Carbon::now()->toDateTimeString();
            DB::connection('lovbee')->table('bgms')->insert($data);
        }
        return response()->json([
            'result' => 'success',
        ]);

    }

    public function destroy($id)
    {
        $data['is_delete'] = 1;
        $data['deleted_at'] = Carbon::now()->toDateTimeString();
        DB::connection('lovbee')->table('bgms')->where('id' , $id)->update($data);
        return response()->json([
            'result' => 'success',
        ]);
    }

}