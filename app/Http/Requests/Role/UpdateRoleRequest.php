<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateRoleRequest extends FormRequest
{
    /**
     * تحديد إذا كان المستخدم مصرح له بتنفيذ هذا الطلب
     */
    public function authorize(): bool
    {
        // // التحقق من صلاحيات المستخدم
        // $role = $this->route('role');
        
        // // منع تعديل الأدوار المحمية
        // $protectedRoles = ['super-admin', 'admin', 'owner', 'system'];
        // if (in_array($role->name, $protectedRoles) && !auth()->user()->hasRole('super-admin')) {
        //     return false;
        // }
        
        return Auth::check() && Auth::user()->can('تعديل صلاحية');
        
        // أو بشكل أبسط أثناء التطوير:
        // return auth()->check();
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        $roleId = $this->route('role')->id;
        $roleName = $this->route('role')->name;
        
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                'min:3',
                Rule::unique('roles', 'name')->ignore($roleId)
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
        $role = $this->route('role');
        
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
        $role = $this->route('role');
        
        // تحويل اسم الدور إلى صيغة قياسية
        if ($this->has('name')) {
            $this->merge([
                'name' =>trim($this->name)
            ]);
        }
        
        // التأكد أن permission هي array
        if ($this->has('permission') && !is_array($this->permission)) {
            $this->merge([
                'permission' => (array) $this->permission
            ]);
        }
        
        // // منع تغيير guard_name للأدوار المحمية
        // $protectedRoles = ['super-admin', 'admin', 'owner', 'system'];
        // if (in_array($role->name, $protectedRoles) && $this->has('guard_name')) {
        //     $this->merge([
        //         'guard_name' => $role->guard_name // إبقاء القيمة الأصلية
        //     ]);
        // }
    }

    /**
     * التحقق الإضافي بعد القواعد الأساسية
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $role = $this->route('role');
            
            // منع تغيير اسم الأدوار المحمية
            $protectedRoles = ['super-admin', 'admin', 'owner', 'system'];
            if (in_array($role->name, $protectedRoles) && $this->name !== $role->name) {
                $validator->errors()->add('name', 'لا يمكن تغيير اسم هذا الدور لأنه محمي');
            }
            
            // تحقق من عدم إزالة جميع الصلاحيات
            if (empty($this->permission)) {
                $validator->errors()->add('permission', 'لا يمكن إزالة جميع الصلاحيات من الدور');
            }
            
            // // تحقق من عدم إزالة صلاحيات أساسية من أدوار معينة
            // if ($role->name === 'super-admin') {
            //     $adminPermission = \Spatie\Permission\Models\Permission::where('name', 'like', '%admin%')->first();
            //     if ($adminPermission && !in_array($adminPermission->id, (array)$this->permission)) {
            //         $validator->errors()->add('permission', 'لا يمكن إزالة صلاحيات الإدارة من دور super-admin');
            //     }
            // }
        });
    }
}