<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StoreRoleRequest extends FormRequest
{
    /**
     * تحديد إذا كان المستخدم مصرح له بتنفيذ هذا الطلب
     */
    public function authorize(): bool
    {
        // التحقق من صلاحيات المستخدم
        return Auth::check() && Auth::user()->can('اضافة صلاحية');
        
        
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'min:3',
                'unique:roles,name'
            ],
            
            'permission' => [
                'required',
                'array',
                'min:1',
            ],
            
            'permission.*' => [
                'required',
                'string',
                Rule::exists('permissions', 'name')
            ],
            
            'description' => [
                'nullable',
                'string',
                'max:500'
            ],

            'guard_name' => [
                'sometimes',
                'string',
                Rule::in(['web', 'api'])
            ]
        ];
    }

    /**
     * رسائل الخطأ المخصصة
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم الدور مطلوب',
            'name.max' => 'اسم الدور يجب ألا يتجاوز 50 حرفاً',
            'name.min' => 'اسم الدور يجب أن يكون 3 أحرف على الأقل',
            'name.regex' => 'اسم الدور يجب أن يحتوي على حروف انجليزية، أرقام، شرطات أو نقاط فقط',
            'name.unique' => 'اسم الدور مستخدم مسبقاً',
            
            'permission.required' => 'يجب اختيار صلاحية واحدة على الأقل',
            'permission.min' => 'يجب اختيار صلاحية واحدة على الأقل',
            'permission.max' => 'لا يمكن اختيار أكثر من 50 صلاحية',
            
            'permission.*.exists' => 'الصلاحية المختارة غير موجودة في النظام',
            
            'description.max' => 'الوصف يجب ألا يتجاوز 500 حرف',
            
            'guard_name.in' => 'نظام الحماية يجب أن يكون web أو api'
        ];
    }

    /**
     * تسمية الحقول بالعربية
     */
    public function attributes(): array
    {
        return [
            'name' => 'اسم الدور',
            'permission' => 'الصلاحيات',
            'description' => 'الوصف',
            'guard_name' => 'نظام الحماية'
        ];
    }

    /**
     * تحضير البيانات قبل التحقق
     */
    protected function prepareForValidation(): void
    {
        // تحويل اسم الدور إلى صيغة قياسية (lowercase, trim)
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->name)
            ]);
        }
        
        // التأكد أن permission هي array
        if ($this->has('permission') && !is_array($this->permission)) {
            $this->merge([
                'permission' => (array) $this->permission
            ]);
        }
        
        // تعيين guard_name افتراضي إذا لم يتم إرساله
        if (!$this->has('guard_name') || empty($this->guard_name)) {
            $this->merge([
                'guard_name' => 'web'
            ]);
        }
    }

    /**
     * معالجة البيانات بعد التحقق
     */
    public function passedValidation(): void
    {
        // يمكنك إضافة معالجات إضافية هنا
        // مثلاً: تحويل الأسماء إلى صيغة معينة
    }
}