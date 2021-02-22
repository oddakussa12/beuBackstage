<?php
namespace App\Http\Controllers\Lovbee;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MessageController extends Controller
{

    private CONST BOSS_ID=290;

    public function index()
    {

    }

    public function operation()
    {
        return  view('backstage.lovbee.message.operation');
    }

    public function submit(Request $request)
    {
        Log::info('all' , array($request->all()));
        $client  = new Client();
        $image   = $request->input('image' , '');
        $video   = $request->input('video' , '');
        $type    = intval($request->input('type' , 0));
        $target  = strval($request->input('target' , ''));
        $sender  = strval($request->input('sender' , ''));
        $country = $request->input('country' , '');
        if(!blank($video)&&!blank($image)&&!blank($sender))
        {
            if($type==0||$type==3)
            {
                $data = [
                    'sender' => $sender,
                    'target' => 0,
                    'type'   => $type,
                    'image'  => $image,
                    'video'  => $video,
                ];
            } elseif ($type==1&&!blank($country))
            {
                $data = [
                    'sender' => $sender,
                    'target' => $country,
                    'type' => $type,
                    'image' => $image,
                    'video' => $video,
                ];
            }elseif ($type==2&&!blank($target))
            {
                $data = [
                    'sender' => $sender,
                    'target' => $target,
                    'type' => $type,
                    'image' => $image,
                    'video' => $video,
                ];
            }else{
                return back();
            }
            $response = $client->request('POST', config('common.lovbee_domain').'api/ry/push', ['form_params'=>$data]);

            $statusCode = $response->getStatusCode();
            Log::info('$statusCode' , array($statusCode));
        }
        return back();
    }

    public function play(Request $request)
    {
        $page  = intval($request->input('page' , 1));
        $month = intval($request->input('month' , 1));
        $page  = $page-1;
        $page  = $page<0?0:$page;
        $message = $this->message($request);
        $messages = $message['result'];
        $from = $message['from'];
        $page = $page+1;
        return  view('backstage.lovbee.message.play' , compact('messages' , 'from' , 'page', 'month'));
    }

    public function video(Request $request)
    {
        $message = $this->message($request);
        $messages = collect($message['result'])->toArray();
        $from = $message['from'];;
        return response(array('messages'=>$messages , 'from'=>$from));
    }

    public function message(Request $request)
    {
        $month = intval($request->input('month' , 1));
        $page  = intval($request->input('page' , 1));
        $page  = $page-1;
        $page  = $page<0 ? 0 : $page;
        $month = $month>12 || $month<0 ? 1 : $month;
        if ($month<10) {
            $month = '0'.strval($month);
        }
        $table = 'ry_messages_'.date('Y').$month;
        $cTable= 'ry_chats_'.date('Y').$month;
        $cTable= Schema::connection('lovbee')->hasTable($cTable) ? $cTable : 'ry_chats';
        $table = Schema::connection('lovbee')->hasTable($table)  ? $table  : 'ry_messages';

        /*$chat  = DB::connection('lovbee')->table($cTable)->where('chat_from_id', $this->bossId)->orWhere('chat_to_id', $this->bossId)
            ->select('chat_msg_uid')->get();
        return DB::connection('lovbee')->table($table)->whereNotIn('message_id', $chat->pluck('chat_msg_uid')->toArray())
            ->where('message_type' , 'Helloo:VideoMsg')->groupBy('message_content')->orderByDesc('id')->offset($page)->limit(1)->get();*/

        $result = DB::connection('lovbee')->table($table)->where('message_type' , 'Helloo:VideoMsg')->groupBy('message_content')->orderByDesc('id')->offset($page)->limit(1)->get();
        $msgId  = $result->pluck('message_id')->toArray();
        $chat   = DB::connection('lovbee')->table($cTable)->where('chat_msg_uid', current($msgId))->select('chat_from_id')->first();
        if ($chat->chat_from_id==self::BOSS_ID) {
            $page = $page+1;
            $request->offsetSet('page', $page);
            return $this->message($request);
        }
        $from = DB::connection('lovbee')->table('users')->where('user_id', $chat->chat_from_id)->first();
        return array('result'=>$result , 'from'=>$from);

    }
}