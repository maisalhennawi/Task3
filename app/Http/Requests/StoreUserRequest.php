<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users',
        'phone_number' => 'required|unique:users',
        'username' => 'required|unique:users',
        'profile_photo' => 'required|image',
        'certificate' => 'required|mimes:pdf',
        'password' => 'required|confirmed|min:6',
        ];
    }
}
