<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateRoleRequest extends BaseFormRequest
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
        $id = $this->route('role');
        return [
            'id' => 'required|numeric|min:1',
            'name' => [
                'required','string','max:255'
            ],
            'role_auth' => 'array',
        ];
    }
}
