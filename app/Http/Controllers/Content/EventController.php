<?php

namespace App\Http\Controllers\Content;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Content\Event;
use App\Models\Content\Banner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class EventController extends Controller
{


    public function index()
    {
        $events = Event::orderByDesc('id')->paginate(10);
        return view('backstage.content.event.index',compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('backstage.content.tag.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $event = $request->only('name' , 'value' , 'type' , 'sort');
        $event['image'] = \json_encode(array());
        $event['name'] = empty($event['name'])?date('Y-m-d-H-i-s'):$event['name'];
        !empty($event['value'])&&$event['value'] = strval($event['value']);
        $event['type'] = strval($event['type']);
        $event['sort'] = intval($event['sort']);
        return Event::create($event);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
//        $tagsedit = $this->tag->find($id);
//        // dd($tagsedit);
//        return view('backstage.content.tag.edit',compact('tagsedit'));
    }

    public function image($id)
    {
        $event = Event::where('id' , $id)->first();
        $images = \json_decode($event->image , true);
        $supportLanguage = config('translatable.frontSupportedLocales');
        return view('backstage.content.event.image',compact('id' , 'supportLanguage' , 'event' , 'images'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        $event = Event::where('id' , $id)->first();
        $data = array();
        if($request->has('flag'))
        {
            $flag = strval($request->input('flag' , 'on'));
            $flag = $flag=='on'?1:0;
            $data['flag'] = $flag;
        }
        if($request->has('status'))
        {
            $status = strval($request->input('status' , 'on'));
            $status = $status=='on'?1:0;
            $data['status'] = $status;
            $status==1&&Event::where('status' , 1)->update(array('status'=>0));
        }

        if($request->has('image'))
        {
            $oldImage = \json_decode($event->image , true);
            $image = strval($request->input('image' , ''));
            $locale = strval($request->input('locale' , 'en'));
            $oldImage[$locale] = $image;
            $data['image'] = \json_encode($oldImage);
        }

        if($request->has('value'))
        {
            $value = strval($request->input('value' , ''));
            $data['value'] = $value;
        }

        if($request->has('sort'))
        {
            $sort = intval($request->input('sort' , 0));
            $data['sort'] = $sort;
        }
        Event::where('id' , $id)->update($data);
        if($request->has('status'))
        {
            $this->setEvent();
        }
        return response()->json([
            'result' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    private function setEvent()
    {
        $params = array(
            'time_stamp'=>time(),
        );
        $url = front_url('api/bk/event');
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
