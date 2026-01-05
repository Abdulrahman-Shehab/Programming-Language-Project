<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.required' => 'حقل تاريخ البدء مطلوب',
            'start_date.date' => 'حقل تاريخ البدء يجب أن يكون تاريخ صحيح',
            'end_date.required' => 'حقل تاريخ الانتهاء مطلوب',
            'end_date.date' => 'حقل تاريخ الانتهاء يجب أن يكون تاريخ صحيح',
            'end_date.after_or_equal' => 'حقل تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ البدء',
        ];
    }
}
