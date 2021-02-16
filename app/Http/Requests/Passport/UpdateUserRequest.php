<?php

namespace App\Http\Requests\Passport;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseFormRequest;

class UpdateUserRequest extends BaseFormRequest
{
    protected $translationsAttributesKey = 'user.validation.attributes';

    public function rules()
    {
        return [
            'user_name' => 'bail|filled',
            'user_email' => 'bail|filled',
            'user_ip_address' => 'bail|filled|ip',
            'user_email_code' => 'bail|filled|string',
            'user_first_name' => 'bail|filled|string|between:5,32',
            'user_last_name' => 'bail|filled|string|between:5,32',
            'user_age' => 'bail|filled|numeric|between:1,150',
            'user_age_changed' => 'bail|filled|numeric|between:1,150',
            'user_language' => 'bail|filled|string|between:5,32',
            'user_country_id' => 'bail|filled|numeric|between:1,150',
            'user_gender' => 'bail|filled|numeric|between:0,2',
            'user_src' => 'bail|filled|string|max:32',
            'user_device_id' => 'bail|filled|string',
            'user_avatar' => 'bail|filled|string',
            'user_cover' => 'bail|filled|string',
            'user_google' => 'bail|filled|string',
            'user_facebook' => 'bail|filled|string',
            'user_twitter' => 'bail|filled|string',
            'user_instagram' => 'bail|filled|string',
            'user_active' => 'bail|filled|numeric|between:0,1',
            'user_verified' => 'bail|filled|numeric|between:0,1',
            'user_is_pro' => 'bail|filled|numeric|between:0,1',
            'user_two_factor' => 'bail|filled|numeric|between:0,1',
            'user_video_mon' => 'bail|filled|numeric|between:0,1',
            'user_imports' => 'bail|filled|numeric',
            'user_uploads' => 'bail|filled|numeric',
            'user_upload_limit' => 'bail|filled|string|between:0,150',
            'user_wallet' => 'bail|filled|numeric',
            'user_balance' => 'bail|filled|numeric',
            'user_donation_paypal_email' => 'bail|filled|email',
            'user_active_time' => 'bail|filled|numeric',
            'user_last_active' => 'bail|filled|date',
            'user_registered' => 'bail|filled|date',
            'user_active_expire' => 'bail|filled|date',
            'user_about' => 'bail|filled',
            'user_level' => [
                'bail',
                'filled',
                Rule::in(['on', 'off' , 'ON' , 'OFF']),
                ],
        ];
    }

    public function translationRules()
    {
        return [
        ];
    }

    public function authorize()
    {
        return true;
    }

//    public function messages()
//    {
//        return [
//            'template.filled' => trans('page::messages.template is filled'),
//            'is_home.unique' => trans('page::messages.only one homepage allowed'),
//        ];
//    }

    public function translationMessages()
    {
        return [
//            'menu_name.filled' => trans('page::messages.title is filled'),
//            'menu_name.filled' => trans('page::messages.slug is filled'),
//            'menu_name.filled' => trans('page::messages.body is filled'),
        ];
    }
}
