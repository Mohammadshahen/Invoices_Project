<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('تعديل منتج');
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
        $productId = $this->route('product') ? $this->route('product')->id : $this->route('id');
        
        return [
            'product_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[\p{Arabic}\p{L}\s\-\.]+$/u', // يقبل العربية والإنجليزية والمسافات والشرطات والنقاط
                Rule::unique('products', 'product_name')->ignore($productId)
            ],
            
            'section_id' => [
                'required',
                'integer',
                'exists:sections,id'
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
            'product_name.regex' => 'اسم المنتج يجب أن يحتوي على أحرف عربية أو إنجليزية فقط',
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
    }

    // /**
    //  * Configure the validator instance.
    //  */
    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         // التحقق الإضافي من أن المنتج لا يمكن نقله لقسم محذوف
    //         if ($this->has('section_id')) {
    //             $sectionExists = \App\Models\Section::where('id', $this->section_id)
    //                 ->exists();
                
    //             if (!$sectionExists) {
    //                 $validator->errors()->add('section_id', 'القسم المحدد غير متاح');
    //             }
    //         }
            
    //         // منع تغيير المنتجات المرتبطة بفواتير قيد المعالجة
    //         $productId = $this->route('product') ? $this->route('product')->id : $this->route('id');
            
    //         if ($productId) {
    //             $hasPendingInvoices = \App\Models\Invoice::where('product_id', $productId)
    //                 ->whereIn('status', ['غير مدفوعة', 'مدفوعة جزئياً'])
    //                 ->exists();
                
    //             if ($hasPendingInvoices && $this->has('section_id') && $this->section_id != \App\Models\Product::find($productId)->section_id) {
    //                 $validator->errors()->add('section_id', 
    //                 'لا يمكن نقل المنتج لقسم آخر لأنه مرتبط بفواتير قيد المعالجة');
    //             }
    //         }
    //     });
    // }
}