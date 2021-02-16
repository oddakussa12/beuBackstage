<?php

namespace App\Http\Requests\Service;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => [
                "required",
                "string",
                Rule::in(array(
                    'text',
                    'h5',
                    'postDetail',
                    'createPost',
                    'topicDetail',
                    'chatDetail',
                    'userDetail',
                ))
            ],
            'value' => 'filled|string',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
//            'image' => 'required|string|max:5000'
        ];
    }
}
