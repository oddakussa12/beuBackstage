<?php
namespace App\Http\Controllers\Service;

use App\Exports\MessageExport;
use App\Exports\UsersExport;
use App\Models\Passport\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class MessageController extends Controller
{

    CONST BOSS_ID=290;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * 导出
     */
    public function export(Request $request)
    {
        ini_set('memory_limit','256M');

        $now       = Carbon::now();
        $startDate = $now->startOfDay()->toDateTimeString();
        $endDate   = $now->endOfDay()->toDateTimeString();
        $params    = $request->all();
        $date      = $request->input('dateTime' , $startDate.' - '.$endDate);
        $allDate   = explode(' - ' , $date);
        $start     = array_shift($allDate);
        $end       = array_pop($allDate);

        if (empty($start) || empty($end)) {
            $start = $startDate;
            $end   = $endDate;
        }
        return  Excel::download(new MessageExport($params), 'message-'.$start.'-'.$end.'.xlsx');
    }


    public function operation()
    {
        return  view('backstage.service.message.operation');
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

    /**
     * @param Request $request
     * @return array
     * 添加评论
     */
    public function comment(Request $request)
    {
        $params = $request->all();
        $time   = date('Y-m-d H:i:s');
        $params['updated_at'] = $time;

        $result = DB::table('message_comments')->where('message_id', $params['message_id'])->first();
        if (!$result) {
            $params['created_at'] = $time;
            $result = DB::table('message_comments')->insert($params);
        } else {
            $result = DB::table('message_comments')->where('message_id', $params['message_id'])->update($params);
        }
        return ['data'=>$result];
    }

    public function play(Request $request)
    {
        $dateTime = date('Y-m-d', time()-86400*2). ' - '. date('Y-m-d', time()-86400);
        return  view('backstage.service.message.play', compact('dateTime'));
    }

    public function video(Request $request)
    {
        $message = $this->message($request);
        $from = $message['from'];
        return response(array('messages'=>$message , 'from'=>$from));
    }

    public function message(Request $request)
    {
        $month = intval($request->input('month' , 1));
        $page  = intval($request->input('page' , 1));
        $page  = $page-1;
        $page  = $page<0 ? 0 : $page;
        $month = $month>12 || $month<0 ? 1 : $month;
        if ($month<10) {
            $month = '0'.$month;
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

        if (empty($chat->chat_from_id)) {
            return array('result'=>false , 'from'=>null);

        }
        if ($chat->chat_from_id==self::BOSS_ID) {
            $page = $page+1;
            $request->offsetSet('page', $page);
            return $this->message($request);
        }
        $from = DB::connection('lovbee')->table('users')->where('user_id', $chat->chat_from_id)->first();

        $newResult = current(collect($result)->toArray());
        if ($newResult) {
            $result = DB::table('message_comments')->where('message_id', $newResult->id)->first();
            $newResult->comment = !empty($result) ? $result->comment : '';
        }
        return array('result'=>(array)$newResult , 'from'=>$from);

    }

    public function chatMessage(Request $request)
    {
        $params = $request->all();
        $params['dateTime'] = $params['dateTime'] ?? date("Y-m");
        $month  = date('Ym', strtotime($params['dateTime']));
        $sort   = !empty($params['sort']) && $params['sort']=='ASC' ? 'orderBy' : 'orderByDesc';

        $mTable = 'ry_messages_'.$month;
        $cTable = 'ry_chats_'.$month;
        $vTable = 'ry_video_messages_'.$month;
        $cTable = Schema::connection('lovbee')->hasTable($cTable) ? $cTable : 'ry_chats';
        $mTable = Schema::connection('lovbee')->hasTable($mTable) ? $mTable  : 'ry_messages';
        $vTable = Schema::connection('lovbee')->hasTable($vTable) ? $vTable : 'ry_video_messages';

        $chat   = DB::connection('lovbee')->table($cTable)
            ->select("$cTable.chat_msg_uid", "$cTable.chat_msg_type", "$cTable.chat_created_at", "$cTable.chat_from_id", "$cTable.chat_to_id", "$mTable.message_content", "$vTable.video_url")
            ->leftjoin($mTable, "$mTable.message_id", '=', "$cTable.chat_msg_uid")
            ->leftjoin($vTable, "$vTable.message_id", '=', "$cTable.chat_msg_uid");

        $chat = $chat->$sort("$cTable.chat_created_at")->paginate(10);

        $fromId = $chat->pluck('chat_from_id')->toArray();
        $toId   = $chat->pluck('chat_to_id')->toArray();
        $userIds= array_unique(array_merge($fromId, $toId));
        $users  = User::select('user_id', 'user_name', 'user_nick_name', 'user_avatar')->whereIn('user_id', $userIds)->get();

        for ($i=1; $i<=200; $i++) {
            $j  = $i<10 ? '00'.$i : ($i>=10 && $i<100 ? '0'.$i : $i);
            $key= "[emo:e000{$j}]";
            $emoImg[] = $key;
            $emoSrc[] = "<img width='30px' height='30px' src=\"/images/emo/ic_chat_e000{$j}.png\"/>";
        }

        foreach ($chat as $item) {
            if ($item->chat_msg_type=='RC:TxtMsg') {
                $item->message_content = str_replace($emoImg, $emoSrc, $item->message_content);
            }
            if ($item->chat_msg_type=='Helloo:VoiceMsg') {
                $item->suffix = substr($item->message_content, -3);
            }
            foreach ($users as $user) {
                if ($item->chat_from_id==$user->user_id) {
                    $item->from = $user;
                }
                if ($item->chat_to_id==$user->user_id) {
                    $item->to = $user;
                }
            }
        }

        $params['result'] = $chat;
        return  view('backstage.service.message.chat', $params);
    }
}