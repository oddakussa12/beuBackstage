<?php

/**
 * @Author: Dell
 * @Date:   2019-08-06 16:26:44
 * @Last Modified by:   Dell
 * @Last Modified time: 2019-08-06 16:36:22
 */
namespace App\Http\Requests\Content;
use App\Http\Requests\BaseFormRequest;

class UpdateTagRequest extends BaseFormRequest
{
    protected $translationsAttributesKey = 'menu.validation.attributes';

    public function rules()
    {
        return [
            'tag_sort' => 'bail|numeric',
            'tag_status' => 'bail|boolean',
            'tag_isdel' => 'bail|numeric',
        ];
    }

    public function translationRules()
    {
        return [
            // 'tag_name' => 'bail|required|string|max:255',
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