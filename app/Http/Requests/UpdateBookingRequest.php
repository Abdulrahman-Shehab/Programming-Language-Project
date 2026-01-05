<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'start_date.date' => 'حقل تاريخ البدء يجب أن يكون تاريخ صحيح',
            'end_date.date' => 'حقل تاريخ الانتهاء يجب أن يكون تاريخ صحيح',
            'end_date.after_or_equal' => 'حقل تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ البدء',
        ];
    }
}
