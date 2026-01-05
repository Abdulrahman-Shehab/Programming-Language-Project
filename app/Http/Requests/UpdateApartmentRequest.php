<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'area' => 'sometimes|numeric|min:0',
            'description' => 'sometimes|string',
            'daily_price' => 'sometimes|numeric|min:0',
            'address' => 'sometimes|string|max:255',
            'governorate_id' => 'sometimes|exists:governorates,id',
            'city_id' => 'sometimes|exists:cities,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'حقل العنوان يجب أن يكون نص',
            'title.max' => 'حقل العنوان يجب أن لا يتجاوز 255 حرف',
            'area.numeric' => 'حقل المساحة يجب أن يكون رقم',
            'area.min' => 'حقل المساحة يجب أن يكون أكبر من 0',
            'description.string' => 'حقل الوصف يجب أن يكون نص',
            'daily_price.numeric' => 'حقل السعر اليومي يجب أن يكون رقم',
            'daily_price.min' => 'حقل السعر اليومي يجب أن يكون أكبر من 0',
            'address.string' => 'حقل العنوان يجب أن يكون نص',
            'address.max' => 'حقل العنوان يجب أن لا يتجاوز 255 حرف',
            'governorate_id.exists' => 'المحافظة المحددة غير موجودة',
            'city_id.exists' => 'المدينة المحددة غير موجودة',
        ];
    }
}
