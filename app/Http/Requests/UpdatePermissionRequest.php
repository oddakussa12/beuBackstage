<?php

namespace App\Http\Requests;


class UpdatePermissionRequest extends BaseFormRequest
{
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
            'id' => 'required|numeric|min:1',
            'name' => [
                'required','string','max:255'
            ]
        ];
    }
}
