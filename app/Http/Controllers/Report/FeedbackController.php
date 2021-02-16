<?php

namespace App\Http\Controllers\Report;

use Illuminate\Http\Request;
use App\Models\Report\Feedback;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{

    public function index(Request $request)
    {
        $feedback_content = $request->input('feedback_content' , '');
        $feedbacks = Feedback::orderBy('feedback_id' , 'DESC');
        if(!empty($feedback_content))
        {
            $feedbacks = $feedbacks->where('feedback_content' , 'like' , '%'.$feedback_content.'%');
        }
        $feedbacks = $feedbacks->paginate(8);
        return view('backstage.feedback.index')->with(['feedbacks'=>$feedbacks ,'feedback_content'=>$feedback_content]);
    }

    public function update(Request $request, Feedback $feedback)
    {
        $feedback->feedback_result = $request->input('feedback_result' , 0);
        $feedback->save();
        return response()->json([
            'result' => 'success',
        ]);
    }

}
