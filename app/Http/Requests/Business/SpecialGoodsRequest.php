<?php

namespace App\Http\Requests\Business;

use Illuminate\Validation\Rule;
use App\Http\Requests\BaseFormRequest;

class SpecialGoodsRequest extends BaseFormRequest
{
    protected $translationsAttributesKey = 'user.validation.attributes';

    public function rules()
    {
        return [
            'goods_id' => 'bail|filled|string',
            'special_price' => 'bail|required|numeric',
            'free_delivery' => [
                'bail',
                'required',
                Rule::in(array(0,1))
            ],
            'packaging_cost' => 'bail|required|numeric',
            'deadline' => [
                'bail',
                'required',
                'date_format:Y-m-d H:i:s'
            ],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
