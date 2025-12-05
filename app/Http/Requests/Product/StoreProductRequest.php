<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('اضافة منتج');
        // أو بشكل أبسط أثناء التطوير:
        // return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[\p{Arabic}\p{L}\s\-\.\_]+$/u', // يقبل العربية والإنجليزية والمسافات والشرطات والنقاط والشرطة السفلية
                'unique:products,product_name'
            ],
            
            'section_id' => [
                'required',
                'integer',
                'exists:sections,id',
                function ($attribute, $value, $fail) {
                    // تحقق من أن القسم فعال وغير محذوف
                    $section = \App\Models\Section::find($value);
                    if (!$section) {
                        $fail('القسم المحدد غير موجود');
                    }
                    // يمكنك إضافة المزيد من التحقق هنا إذا كان لديك حقل status للأقسام
                }
            ],
            
            'description' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_name.required' => 'اسم المنتج مطلوب',
            'product_name.max' => 'اسم المنتج يجب ألا يتجاوز 255 حرفاً',
            'product_name.min' => 'اسم المنتج يجب أن يكون 3 أحرف على الأقل',
            'product_name.regex' => 'اسم المنتج يجب أن يحتوي على أحرف عربية أو إنجليزية فقط (يسمح بالمسافات، الشرطات، النقاط، والشرطة السفلية)',
            'product_name.unique' => 'اسم المنتج موجود بالفعل في النظام',
            
            'section_id.required' => 'يرجى اختيار القسم',
            'section_id.exists' => 'القسم المحدد غير موجود في النظام',
            
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_name' => 'اسم المنتج',
            'section_id' => 'القسم',
            'description' => 'الوصف'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // تنظيف البيانات
        if ($this->has('product_name')) {
            $this->merge([
                'product_name' => trim($this->product_name)
            ]);
        }
        
        if ($this->has('description')) {
            $this->merge([
                'description' => trim($this->description) ?: null
            ]);
        }
        
        // تحويل section_id إلى integer إذا كان موجوداً
        if ($this->has('section_id') && is_numeric($this->section_id)) {
            $this->merge([
                'section_id' => (int) $this->section_id
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         // تحقق إضافي: منع إنشاء منتجات في أقسام معينة
    //         if ($this->has('section_id')) {
    //             $restrictedSections = []; // يمكنك إضافة IDs للأقسام المحظورة هنا
                
    //             if (in_array($this->section_id, $restrictedSections)) {
    //                 $validator->errors()->add('section_id', 
    //                     'لا يمكن إنشاء منتجات في هذا القسم');
    //             }
    //         }
            
    //         // تحقق: الحد الأقصى للمنتجات في القسم الواحد
    //         if ($this->has('section_id')) {
    //             $productsCount = \App\Models\Product::where('section_id', $this->section_id)->count();
    //             $maxProductsPerSection = 100; // يمكنك تعديل الرقم حسب احتياجك
                
    //             if ($productsCount >= $maxProductsPerSection) {
    //                 $validator->errors()->add('section_id', 
    //                     'وصل هذا القسم للحد الأقصى للمنتجات (' . $maxProductsPerSection . ')');
    //             }
    //         }
    //     });
    // }
}