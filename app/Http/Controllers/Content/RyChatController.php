<?php

namespace App\Http\Controllers\Content;

use Illuminate\Http\Request;
use App\Models\Content\RyChat;
use App\Models\Passport\User;
use App\Http\Controllers\Controller;

class RyChatController extends Controller
{

    public function index(Request $request)
    {
        $params = $request->all();
        $limit = 12;
        $params['limit']=$limit;
        $time = time()*1000;
        $page = intval($request->input('page' , 1));
        $query_time = intval($request->input('query_time' , $time));
        $name = strval($request->input('name' , ''));
        $params['name']=$name;
        $query_time = $page===1?$time:$query_time;
        $params['query_time']=$query_time;
        $params['appends'] = $params;
        $chats = RyChat::where('chat_time' , '<=' , $query_time)->select('chat_id' , 'chat_from_id' , 'chat_from_name' , 'chat_to_id' , 'chat_msg_type' , 'chat_created_at')->orderBy('chat_time' , 'DESC');// chat_content, chat_image
        if(!empty($name))
        {
            $user = User::where('user_name' , $name)->first();
            if(!empty($user))
            {
                $chats = $chats->where('chat_from_id' , $user->user_id);
            }else{
                $chats = $chats->where('chat_id' , 0);
            }
        }
        $chats = $chats->paginate($limit);
        $params['chats']=$chats;
        return view('backstage.content.ry_chat.index' , compact('chats') , $params);
    }

}
