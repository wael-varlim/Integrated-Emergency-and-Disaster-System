<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name'                  => 'required|string|max:255',
            'last_name'                   => 'required|string|max:255',
            'email'                       => 'required|email:rfc,dns',
            'official_identifier_method'  => 'required|string|in:passport,national_id',
            'official_identifier'         => 'required|string|unique:known_users,official_identifier',
            'address'                     => 'required|string',
            'password'                    => 'required|string|min:8|confirmed',
            'device_name'                 => 'required|string|max:255',
        ];
    }
}
