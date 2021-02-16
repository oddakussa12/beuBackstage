<?php

namespace App\Http\Requests;

class UpdateMenuRequest extends BaseFormRequest
{
    protected $translationsAttributesKey = 'menu.validation.attributes';

    public function rules()
    {
        return [
//            'primary' => "unique:menus,primary,{$menu->id}",
            'menu_auth' => 'bail|max:255',
            'menu_url' => 'bail|max:255',
            'menu_p_id' => 'bail|numeric',
        ];
    }

    public function translationRules()
    {
        return [
            'menu_name' => 'bail|required|string|max:255',
        ];
    }

    public function authorize()
    {
        return true;
    }

//    public function messages()
//    {
//        return [
//            'template.required' => trans('page::messages.template is required'),
//            'is_home.unique' => trans('page::messages.only one homepage allowed'),
//        ];
//    }

    public function translationMessages()
    {
        return [
//            'menu_name.required' => trans('page::messages.title is required'),
//            'menu_name.required' => trans('page::messages.slug is required'),
//            'menu_name.required' => trans('page::messages.body is required'),
        ];
    }
}
