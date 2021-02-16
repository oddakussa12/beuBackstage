<?php

namespace App\Http\Controllers\Service;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Service\Message;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\StoreMessageRequest;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Throwable
     */
    public function index()
    {
        $messages = Message::orderByDesc('id')->paginate(10);
        return view('backstage.service.message.index',compact('messages'));
    }

    public function store(StoreMessageRequest $request)
    {
        $all = $request->only(array("type" , "value" , 'title' , 'content' , 'image'));
        Message::create($all);
        return response()->json([
            'result' => 'success',
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $message = Message::where('id' , $id)->first();
        if($request->has('status'))
        {
            $status = strval($request->input('status' , 'on'));
            $readyRunMessage = Message::orderByDesc('id')->whereIn('status' , array(1 , 2))->first();
            if(!empty($readyRunMessage))
            {
                abort(403 , 'A task is preparing or running');
            }
            if($status=='on'&&$message->status==0)
            {
                $message->status=1;
                $message->save();
                $this->setMessage($id);
            }
        }
        if($request->has('image'))
        {
            $oldImage = \json_decode($message->image , true);
            $image = strval($request->input('image' , ''));
            $locale = strval($request->input('locale' , 'en'));
            $oldImage[$locale] = $image;
            $message->image = \json_encode($oldImage);
            $message->save();
        }
        return response()->json([
            'result' => 'success',
        ]);
    }

    public function image($id)
    {
        $message = Message::where('id' , $id)->first();
        $images = \json_decode($message->image , true);
        $supportLanguage = config('translatable.frontSupportedLocales');
        return view('backstage.service.message.image',compact('id' , 'supportLanguage' , 'message' , 'images'));
    }

    private function setMessage($messageId)
    {
        $params = array(
            'message_id'=>$messageId,
            'time_stamp'=>time(),
        );
        $url = front_url('api/bk/service/message');
        $signature = common_signature($params);
        $params['signature'] = $signature;
        $data = [
            'form_params' => $params
        ];
        $client = new Client();
        try {
            $response = $client->request('PATCH', $url , $data);
            $code = $response->getStatusCode();
            if($code!=204)
            {
                abort($code);
            }
        } catch (GuzzleException $e) {
            abort(400 , $e->getMessage());
        }
    }

}
