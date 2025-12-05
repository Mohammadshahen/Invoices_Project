<?php

namespace App\Http\Requests\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // تأكد من أن المستخدم الحالي لديه صلاحية إنشاء مستخدمين
        // تأكد أولاً من أن المستخدم مصدق عليه ثم تحقق من الصلاحية
        return Auth::check() && Auth::user()->can('اضافة مستخدم');
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // 'regex:/^[\p{Arabic}\s]+$/u' // يقبل الأحرف العربية والمسافات فقط
            ],
            
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],
            
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                // 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/' // كلمة مرور قوية
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
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'اسم المستخدم مطلوب',
            'name.regex' => 'اسم المستخدم يجب أن يحتوي على أحرف عربية فقط',
            
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'كلمات المرور غير متطابقة',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير، حرف صغير، رقم ورمز خاص',
            
            'status.required' => 'حالة المستخدم مطلوبة',
            'status.in' => 'حالة المستخدم غير صحيحة',
            
            'roles.required' => 'يجب اختيار دور واحد على الأقل',
            'roles.min' => 'يجب اختيار دور واحد على الأقل',
            
            'roles.*.exists' => 'الدور المختار غير صحيح'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'اسم المستخدم',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
            'status' => 'حالة المستخدم',
            'roles' => 'الأدوار'
        ];
    }

        protected function prepareForValidation(): void
    {
        // تنظيف البيانات (trim المسافات)
        $this->merge([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
        ]);
    }
}
