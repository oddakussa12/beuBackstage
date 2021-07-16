<?php
namespace App\Http\Controllers\Operation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        $feedbacks = DB::connection('lovbee')->table('feedback')->leftJoin('users', 'users.user_id', '=', "feedback.user_id");
        if(isset($params['dateTime']))
        {
            $dateTime = $this->parseTime($params['dateTime']);
            if($dateTime!==false)
            {
                $feedbacks->whereBetween('created_at' , array($dateTime['start'] , $dateTime['end']));
            }
        }
        $feedbacks = $feedbacks->select(DB::raw("t_feedback.*"), 'users.user_name','users.user_nick_name','users.user_avatar');

        if (isset($params['status'])&&in_array($params['status'], [0, 1, '0', '1'])) {
            $feedbacks = $feedbacks->where("feedback.status", $params['status']);
        }
        if (!empty($params['keyword'])) {
            if (!empty($params['keyword'])) {
                $keyword = trim($params['keyword']);
                $feedbacks  = $feedbacks->where(function($query)use($keyword){$query->where('users.user_name', 'like', "{$keyword}%")->orWhere('user_nick_name', 'like', "{$keyword}%");});
            }
        }
        $feedbacks = $feedbacks->orderByDesc('id')->paginate(10)->appends($params);
        foreach ($feedbacks as $item) {
            $item->image = !empty($item->image) ? explode(';', $item->image) : [];
        }

        $params['feedbacks'] = $feedbacks;
        return view('backstage.operation.feedback.index', $params);
    }

}