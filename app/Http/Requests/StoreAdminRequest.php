<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
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
            'admin_email' => 'required|email|unique:admins|max:255',
            'admin_username' => 'required|string|max:300',
            'admin_realname' => 'required|string|max:255',
            'admin_sex' => 'required|numeric|between:0,2',
            'admin_status' => 'required|boolean',
            'admin_roles' => 'required|array',
            'admin_auth' => 'required|array',
        ];
    }
}
