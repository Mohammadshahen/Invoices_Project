<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // تحقق من صلاحية المستخدم لتعديل قسم
        return Auth::check() && Auth::user()->can('تعديل قسم');
        // أو بشكل أبسط:
        // return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $sectionId = $this->route('section') ? $this->route('section')->id : $this->route('id');
        
        return [
            'section_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[\p{Arabic}\p{L}\s\-\.]+$/u',
                Rule::unique('sections', 'section_name')->ignore($sectionId)
            ],
            
            'description' => [
                'required',
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
            'section_name.required' => 'يجب ادخال اسم القسم',
            'section_name.max' => 'اسم القسم يجب ألا يتجاوز 255 حرفاً',
            'section_name.min' => 'اسم القسم يجب أن يكون 3 أحرف على الأقل',
            'section_name.regex' => 'اسم القسم يجب أن يحتوي على أحرف عربية أو إنجليزية فقط',
            'section_name.unique' => 'القسم موجود بالفعل',
            
            'description.required' => 'يجب ادخال وصف للقسم',
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'section_name' => 'اسم القسم',
            'description' => 'الوصف'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // تنظيف البيانات قبل التحقق
        if ($this->has('section_name')) {
            $this->merge([
                'section_name' => trim($this->section_name)
            ]);
        }
        
        if ($this->has('description')) {
            $this->merge([
                'description' => trim($this->description)
            ]);
        }
    }

    /**
     * Configure the validator instance.
     */
    // public function withValidator($validator)
    // {
    //     $validator->after(function ($validator) {
    //         // يمكنك إضافة تحقيقات إضافية هنا
    //         // مثلاً: منع تعديل قسم مرتبط بمنتجات
    //         $sectionId = $this->route('section') ? $this->route('section')->id : $this->route('id');
            
    //         if ($sectionId) {
    //             // مثال: التحقق من أن القسم غير محذوف
    //             $sectionExists = \App\Models\Section::where('id', $sectionId)->exists();
                
    //             if (!$sectionExists) {
    //                 $validator->errors()->add('section_id', 'القسم المحدد غير موجود');
    //             }
                
    //             // مثال: منع تغيير اسم قسم مرتبط بمنتجات نشطة
    //             // $hasActiveProducts = \App\Models\Product::where('section_id', $sectionId)
    //             //     ->where('status', 'active')
    //             //     ->exists();
                
    //             // if ($hasActiveProducts && $this->section_name != \App\Models\Section::find($sectionId)->section_name) {
    //             //     $validator->errors()->add('section_name', 
    //             //         'لا يمكن تغيير اسم القسم لأنه مرتبط بمنتجات نشطة');
    //             // }
    //         }
    //     });
    // }
}