<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginCredentialRequest extends FormRequest
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
            'full_name'      => 'required|max:150',
            'email'  => 'required|max:150|email',
            'office_name'      => 'required|max:100',
            'office_address'      => 'required|max:150',
            'office_phone'      => 'required|max:30',
            'designation'      => 'required|max:50',
            'captcha' => 'required'
        ];
    }
}
