<?php

namespace App\Http\Requests;

class StoreConfigRequest extends BaseFormRequest
{
    protected $translationsAttributesKey = 'menu.validation.attributes';

    public function rules()
    {
        return [
            'set.general.api.youtube_api' => 'bail|max:255',
            'set.general.api.dailymotion_id' => 'bail|max:255',
            'set.general.api.twitch_id' => 'bail|numeric',
            'post_rate' => 'bail|numeric|between:0.01,2.0',
        ];
    }


    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
//            'template.required' => trans('page::messages.template is required'),
//            'is_home.unique' => trans('page::messages.only one homepage allowed'),
        ];
    }

}
