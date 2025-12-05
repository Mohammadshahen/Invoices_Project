<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UpdateUserRequest extends FormRequest
{
    /**
     * تحديد إذا كان المستخدم مصرح له بتنفيذ هذا الطلب
     */
    public function authorize(): bool
    {
        // التحقق من صلاحيات المستخدم
        return Auth::check() && (Auth::user()->can('تعديل مستخدم') || Auth::user()->id == $this->route('user')->id);
        
        // أو بشكل أبسط أثناء التطوير:
        // return auth()->check();
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id; // الحصول على معرف المستخدم من الرابط
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\p{Arabic}\p{L}\s\.\-]+$/u' // يقبل العربية والإنجليزية والمسافات والنقاط والشرطات
            ],
            
            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            
            'password' => [
                'nullable', // غير مطلوب عند التحديث
                'string',
                'min:8',
                'confirmed',
                // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/' // حرف صغير، حرف كبير، رقم
            ],
            
            'status' => [
                'required',
                Rule::in(['active', 'inactive'])
            ],
            
            'roles' => [
                'required',
                'array',
                'min:1'
            ],
            
            'roles.*' => [
                'required',
                'string',
                Rule::exists('roles', 'name')
            ]
        ];
    }

    /**
     * رسائل الخطأ المخصصة
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم المستخدم مطلوب',
            'name.regex' => 'اسم المستخدم يجب أن يحتوي على أحرف فقط (عربية/إنجليزية)',
            
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل مستخدم آخر',
            
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمات المرور غير متطابقة',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير، حرف صغير ورقم على الأقل',
            
            'status.required' => 'حالة المستخدم مطلوبة',
            'status.in' => 'حالة المستخدم يجب أن تكون: مفعل، غير مفعل، active، inactive، pending أو suspended',
            
            'roles.required' => 'يجب اختيار دور واحد على الأقل',
            'roles.min' => 'يجب اختيار دور واحد على الأقل',
            
            'roles.*.exists' => 'الدور المختار غير موجود في النظام'
        ];
    }

    /**
     * تسمية الحقول بالعربية
     */
    public function attributes(): array
    {
        return [
            'name' => 'اسم المستخدم',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
            'password_confirmation' => 'تأكيد كلمة المرور',
            'status' => 'حالة المستخدم',
            'roles' => 'الأدوار'
        ];
    }

    /**
     * تحضير البيانات قبل التحقق
     */
    protected function prepareForValidation(): void
    {
        // إذا كانت كلمة المرور فارغة، أزلها من البيانات
        if (empty($this->password)) {
            $this->request->remove('password');
            $this->request->remove('password_confirmation');
        }
        
        // تنظيف البيانات (trim المسافات)
        $this->merge([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
        ]);
    }
}