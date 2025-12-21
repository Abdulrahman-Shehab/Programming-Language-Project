<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class RegisterUserRequest extends FormRequest
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
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'phone' => 'required|string|unique:users,phone',
        'birth_date' => 'required|date|date_format:Y-m-d',
        'password' => 'required|string|min:8|confirmed',
        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'id_card' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'phone.required' => 'Phone number is required',
            'phone.unique' => 'This phone number is already registered',
            'birth_date.required' => 'Birth date is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'profile_image.image' => 'Profile image must be an image file',
            'profile_image.max' => 'Profile image size must not exceed 2MB',
            'id_card.image' => 'ID card image must be an image file',
            'id_card.max' => 'ID card image size must not exceed 2MB',
        ];
    }
}
