<?php

namespace App\Http\Controllers\Content;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Content\Banner;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{


    public function index()
    {
        $banners = Banner::orderByDesc('id')->paginate(10);
        return view('backstage.content.banner.index',compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backstage.content.tag.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $banner = $request->only('value' , 'type' , 'sort');
        $banner['image'] = \json_encode(array());
        return Banner::create($banner);
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
//        $tagsedit = $this->tag->find($id);
//        // dd($tagsedit);
//        return view('backstage.content.tag.edit',compact('tagsedit'));
    }

    public function image($id)
    {
        $banner = Banner::where('id' , $id)->first();
        $images = \json_decode($banner->image , true);
        $supportLanguage = config('translatable.frontSupportedLocales');
        return view('backstage.content.banner.image',compact('id' , 'supportLanguage' , 'banner' , 'images'));
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
        $banner = Banner::where('id' , $id)->first();
        $data = array();
        if($request->has('status'))
        {
            $status = strval($request->input('status' , 'on'));
            $status = $status=='on'?1:0;
            $data['status'] = $status;
        }

        if($request->has('repeat'))
        {
            $repeat = strval($request->input('repeat' , 'on'));
            $repeat = $repeat=='on'?1:0;
            $data['repeat'] = $repeat;
        }

        if($request->has('image'))
        {
            $oldImage = \json_decode($banner->image , true);
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

        Banner::where('id' , $id)->update($data);
        if($request->has('status'))
        {
            $this->setBanner();
        }
        return response()->json([
            'result' => 'success',
        ]);
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

    private function setBanner()
    {
        $params = array(
            'time_stamp'=>time(),
        );
        $url = front_url('api/bk/banner');
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
