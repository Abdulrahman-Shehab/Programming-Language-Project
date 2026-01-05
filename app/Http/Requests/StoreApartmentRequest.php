<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'area' => 'required|numeric|min:0',
            'description' => 'required|string',
            'daily_price' => 'required|numeric|min:0',
            'address' => 'required|string|max:255',
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'required|exists:cities,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'حقل العنوان مطلوب',
            'title.string' => 'حقل العنوان يجب أن يكون نص',
            'title.max' => 'حقل العنوان يجب أن لا يتجاوز 255 حرف',
            'area.required' => 'حقل المساحة مطلوب',
            'area.numeric' => 'حقل المساحة يجب أن يكون رقم',
            'area.min' => 'حقل المساحة يجب أن يكون أكبر من 0',
            'description.required' => 'حقل الوصف مطلوب',
            'description.string' => 'حقل الوصف يجب أن يكون نص',
            'daily_price.required' => 'حقل السعر اليومي مطلوب',
            'daily_price.numeric' => 'حقل السعر اليومي يجب أن يكون رقم',
            'daily_price.min' => 'حقل السعر اليومي يجب أن يكون أكبر من 0',
            'address.required' => 'حقل العنوان مطلوب',
            'address.string' => 'حقل العنوان يجب أن يكون نص',
            'address.max' => 'حقل العنوان يجب أن لا يتجاوز 255 حرف',
            'governorate_id.required' => 'حقل المحافظة مطلوب',
            'governorate_id.exists' => 'المحافظة المحددة غير موجودة',
            'city_id.required' => 'حقل المدينة مطلوب',
            'city_id.exists' => 'المدينة المحددة غير موجودة',
        ];
    }
}
