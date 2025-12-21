<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApartmentRequest extends FormRequest
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
            'governorate_id' => 'required|exists:governorates,id',
            'city_id' => 'required|exists:cities,id',
            'title' => 'required|string|max:255',
            'area' => 'required|numeric|min:0',
            'description' => 'required|string',
            'daily_price' => 'required|numeric|min:0',
            'address' => 'required|string',
            'image1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image5' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'governorate_id.required' => 'معرف المحافظة مطلوب',
            'governorate_id.exists' => 'المحافظة المحددة غير موجودة',
            'city_id.required' => 'معرف المدينة مطلوب',
            'city_id.exists' => 'المدينة المحددة غير موجودة',
            'title.required' => 'عنوان الشقة مطلوب',
            'title.string' => 'عنوان الشقة يجب أن يكون نصاً',
            'title.max' => 'عنوان الشقة يجب ألا يتجاوز 255 حرفاً',
            'area.required' => 'مساحة الشقة مطلوبة',
            'area.numeric' => 'مساحة الشقة يجب أن تكون رقماً',
            'area.min' => 'مساحة الشقة يجب أن تكون أكبر من أو تساوي 0',
            'description.required' => 'وصف الشقة مطلوب',
            'description.string' => 'وصف الشقة يجب أن يكون نصاً',
            'daily_price.required' => 'السعر اليومي مطلوب',
            'daily_price.numeric' => 'السعر اليومي يجب أن يكون رقماً',
            'daily_price.min' => 'السعر اليومي يجب أن يكون أكبر من أو يساوي 0',
            'address.required' => 'عنوان الشقة مطلوب',
            'address.string' => 'عنوان الشقة يجب أن يكون نصاً',
            'image1.image' => 'الصورة الأولى يجب أن تكون ملف صورة',
            'image1.mimes' => 'الصورة الأولى يجب أن تكون من نوع: jpeg, png, jpg, gif',
            'image1.max' => 'حجم الصورة الأولى يجب ألا يتجاوز 2MB',
            'image2.image' => 'الصورة الثانية يجب أن تكون ملف صورة',
            'image2.mimes' => 'الصورة الثانية يجب أن تكون من نوع: jpeg, png, jpg, gif',
            'image2.max' => 'حجم الصورة الثانية يجب ألا يتجاوز 2MB',
            'image3.image' => 'الصورة الثالثة يجب أن تكون ملف صورة',
            'image3.mimes' => 'الصورة الثالثة يجب أن تكون من نوع: jpeg, png, jpg, gif',
            'image3.max' => 'حجم الصورة الثالثة يجب ألا يتجاوز 2MB',
            'image4.image' => 'الصورة الرابعة يجب أن تكون ملف صورة',
            'image4.mimes' => 'الصورة الرابعة يجب أن تكون من نوع: jpeg, png, jpg, gif',
            'image4.max' => 'حجم الصورة الرابعة يجب ألا يتجاوز 2MB',
            'image5.image' => 'الصورة الخامسة يجب أن تكون ملف صورة',
            'image5.mimes' => 'الصورة الخامسة يجب أن تكون من نوع: jpeg, png, jpg, gif',
            'image5.max' => 'حجم الصورة الخامسة يجب ألا يتجاوز 2MB',
        ];
    }
}
