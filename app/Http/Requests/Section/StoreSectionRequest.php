<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // تحقق من صلاحية المستخدم لإضافة قسم
        return Auth::check() && Auth::user()->can('اضافة قسم');
        // أو بشكل أبسط:
        // return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'section_name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[\p{Arabic}\p{L}\s\-\.]+$/u', // يقبل العربية والإنجليزية
                Rule::unique('sections', 'section_name')
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
        $this->merge([
            'section_name' => trim($this->section_name),
            'description' => trim($this->description)
        ]);
    }
}