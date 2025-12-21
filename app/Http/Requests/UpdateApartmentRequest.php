<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\City;

class UpdateApartmentRequest extends FormRequest
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
            'user_id' => 'prohibited',
            'username' => 'prohibited',
            'governorate_id' => 'sometimes|exists:governorates,id',
            'city_id' => [
                'sometimes',
                'exists:cities,id',
                Rule::when($this->governorate_id, [
                    'required',
                    Rule::exists('cities', 'id')->where(function ($query) {
                        return $query->where('governorate_id', $this->governorate_id);
                    })
                ]),
                // Ensure city belongs to the governorate if both are provided
                Rule::when($this->governorate_id && $this->city_id, function ($attribute, $value) {
                    $city = City::find($this->city_id);
                    return $city && $city->governorate_id == $this->governorate_id;
                }, 'City must belong to the selected governorate')
            ],
            'title' => 'sometimes|string|max:255',
            'area' => 'sometimes|numeric|min:0',
            'description' => 'sometimes|string',
            'daily_price' => 'sometimes|numeric|min:0',
            'address' => 'sometimes|string',
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
            'user_id.prohibited' => 'غير مسموح بتعديل مالك الشقة',
            'username.prohibited' => 'غير مسموح بتعديل اسم مستخدم مالك الشقة',
            'governorate_id.exists' => 'المحافظة المحددة غير موجودة',
            'city_id.exists' => 'المدينة المحددة غير موجودة',
            'city_id.required' => 'يجب تحديد مدينة تابعة للمحافظة المختارة',
            'city_id' => 'المدينة يجب أن تكون تابعة للمحافظة المحددة',
            'title.string' => 'عنوان الشقة يجب أن يكون نصاً',
            'title.max' => 'عنوان الشقة يجب ألا يتجاوز 255 حرفاً',
            'area.numeric' => 'مساحة الشقة يجب أن تكون رقماً',
            'area.min' => 'مساحة الشقة يجب أن تكون أكبر من أو تساوي 0',
            'description.string' => 'وصف الشقة يجب أن يكون نصاً',
            'daily_price.numeric' => 'السعر اليومي يجب أن يكون رقماً',
            'daily_price.min' => 'السعر اليومي يجب أن يكون أكبر من أو يساوي 0',
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
