<?php

/**
 * @Author: Dell
 * @Date:   2019-08-03 18:25:34
 * @Last Modified by:   Dell
 * @Last Modified time: 2019-08-05 14:04:33
 */
namespace App\Http\Requests\Content;
use App\Http\Requests\BaseFormRequest;

class UpdateCategoryRequest extends BaseFormRequest
{
    protected $translationsAttributesKey = 'menu.validation.attributes';

    public function rules()
    {
        return [
            'category_sort' => 'bail|numeric',
            'category_status' => 'bail|boolean',
            'category_isdel' => 'bail|numeric',
        ];
    }

    public function translationRules()
    {
        return [
            // 'category_name' => 'bail|required|string|max:255',
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