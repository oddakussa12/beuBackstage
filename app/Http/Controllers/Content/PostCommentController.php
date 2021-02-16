<?php

namespace App\Http\Controllers\Content;

use App\Traits\PostTrait;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\Contracts\Content\PostCommentRepository;

class PostCommentController extends Controller
{
    use PostTrait;



    private $postComment;

    public function __construct(PostCommentRepository $postComment)
    {
        $this->postComment = $postComment;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $time = time();
        $client = new Client();
        $params = array(
            'comment_id'=>$id,
            'time_stamp'=>$time
        );
        $signature = common_signature($params);
        $params['signature'] = $signature;
        $url = front_url('api/bk/postComment/');
        $data = [
            'form_params' => $params
        ];
        try {
            $response = $client->request('DELETE', $url.$id, $data);
            $code = $response->getStatusCode();
            if($code!=204)
            {
                abort($code);
            }


        } catch (GuzzleException $e) {
            abort($e->getCode() , $e->getMessage());
        }
        return response()->json([
            'result' => 'success',
        ]);
    }


}
