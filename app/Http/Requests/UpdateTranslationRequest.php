<?php

namespace App\Http\Requests;


class UpdateTranslationRequest extends BaseFormRequest
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
            'locale' => [
                'required','string','max:255'
            ],
            'translation_value' => 'required|string',
        ];
    }
}
