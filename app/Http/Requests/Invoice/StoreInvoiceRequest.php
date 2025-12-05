<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * تحديد إذا كان المستخدم مصرح له بتنفيذ هذا الطلب
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('اضافة فاتورة');
        
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        $today = Carbon::today()->format('Y-m-d');
        $maxDate = Carbon::today()->addYears(5)->format('Y-m-d');
        
        return [
            // معلومات الفاتورة الأساسية
            'invoice_number' => [
                'required',
                'string',
                'max:50',
                'unique:invoices,invoice_number'
            ],
            
            'invoice_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:due_date',
                'after_or_equal:' . Carbon::today()->subMonths(3)->format('Y-m-d') // لا يزيد عن 3 أشهر ماضية
            ],
            
            'due_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:invoice_date',
                'before_or_equal:' . $maxDate // لا يزيد عن 5 سنوات مستقبلية
            ],
            
            // الأقسام والمنتجات
            'section_id' => [
                'required',
                'integer',
                'exists:sections,id'
            ],
            
            'product_id' => [
                'required',
                'integer',
                'exists:products,id'
            ],
            
            // المبالغ المالية
            'amount_collection' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/' // رقم مع خانتين عشريتين كحد أقصى
            ],
            
            'amount_commission' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            
            'discount' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            
            'rate_vat' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            
            'value_vat' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            
            'total' => [
                'required',
                'numeric',
                'min:0.01', // لا يمكن أن يكون صفر
                'max:999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            
            // الملاحظات والمرفقات
            'note' => [
                'nullable',
                'string',
                'max:1000'
            ],
            
            'pic' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
                'max:10240', // 10MB كحد أقصى
                'dimensions:min_width=100,min_height=100' // للصور فقط
            ]
        ];
    }

    /**
     * رسائل الخطأ المخصصة
     */
    public function messages(): array
    {
        return [
            // معلومات الفاتورة
            'invoice_number.required' => 'رقم الفاتورة مطلوب',
            'invoice_number.unique' => 'رقم الفاتورة مستخدم مسبقاً',
            'invoice_number.max' => 'رقم الفاتورة يجب ألا يتجاوز 50 حرفاً',
            
            'invoice_date.required' => 'تاريخ الفاتورة مطلوب',
            'invoice_date.before_or_equal' => 'تاريخ الفاتورة يجب أن يكون قبل أو يساوي تاريخ الاستحقاق',
            'invoice_date.after_or_equal' => 'تاريخ الفاتورة لا يمكن أن يكون قبل 3 أشهر',
            
            'due_date.required' => 'تاريخ الاستحقاق مطلوب',
            'due_date.after' => 'تاريخ الاستحقاق يجب أن يكون بعد تاريخ الفاتورة',
            'due_date.before_or_equal' => 'تاريخ الاستحقاق لا يمكن أن يتجاوز 5 سنوات',
            
            // الأقسام والمنتجات
            'section_id.required' => 'القسم مطلوب',
            'section_id.exists' => 'القسم المحدد غير موجود',
            
            'product_id.required' => 'المنتج مطلوب',
            'product_id.exists' => 'المنتج المحدد غير موجود',
            
            // المبالغ المالية
            'amount_collection.required' => 'مبلغ التحصيل مطلوب',
            'amount_collection.regex' => 'مبلغ التحصيل يجب أن يكون رقماً به خانتان عشريتان كحد أقصى',
            
            'amount_commission.required' => 'مبلغ العمولة مطلوب',
            'amount_commission.regex' => 'مبلغ العمولة يجب أن يكون رقماً به خانتان عشريتان كحد أقصى',
            
            'discount.required' => 'مبلغ الخصم مطلوب',
            'discount.regex' => 'مبلغ الخصم يجب أن يكون رقماً به خانتان عشريتان كحد أقصى',
            
            'rate_vat.required' => 'نسبة الضريبة المطلوبة',
            'rate_vat.max' => 'نسبة الضريبة لا يمكن أن تتجاوز 100%',
            'rate_vat.regex' => 'نسبة الضريبة يجب أن تكون رقماً به خانتان عشريتان كحد أقصى',
            
            'value_vat.required' => 'قيمة الضريبة مطلوبة',
            'value_vat.regex' => 'قيمة الضريبة يجب أن تكون رقماً به خانتان عشريتان كحد أقصى',
            
            'total.required' => 'المبلغ الإجمالي مطلوب',
            'total.min' => 'المبلغ الإجمالي يجب أن يكون أكبر من صفر',
            'total.regex' => 'المبلغ الإجمالي يجب أن يكون رقماً به خانتان عشريتان كحد أقصى',
            
            // الملاحظات والمرفقات
            'note.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
            
            'pic.file' => 'يجب أن يكون الملف صالحاً',
            'pic.mimes' => 'نوع الملف غير مسموح به. المسموح: jpg, jpeg, png, pdf, doc, docx, xls, xlsx',
            'pic.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت',
            'pic.dimensions' => 'أبعاد الصورة يجب أن تكون 100×100 بكسل على الأقل'
        ];
    }

    /**
     * تسمية الحقول بالعربية
     */
    public function attributes(): array
    {
        return [
            'invoice_number' => 'رقم الفاتورة',
            'invoice_date' => 'تاريخ الفاتورة',
            'due_date' => 'تاريخ الاستحقاق',
            'section_id' => 'القسم',
            'product_id' => 'المنتج',
            'amount_collection' => 'مبلغ التحصيل',
            'amount_commission' => 'مبلغ العمولة',
            'discount' => 'الخصم',
            'rate_vat' => 'نسبة الضريبة',
            'value_vat' => 'قيمة الضريبة',
            'total' => 'المبلغ الإجمالي',
            'note' => 'الملاحظات',
            'pic' => 'المرفق'
        ];
    }

    /**
     * تحضير البيانات قبل التحقق
     */
    protected function prepareForValidation(): void
    {
        // تنظيف الأرقام (إزالة الفواصل)
        $numericFields = [
            'amount_collection',
            'amount_commission',
            'discount',
            'rate_vat',
            'value_vat',
            'total'
        ];
        
        foreach ($numericFields as $field) {
            if ($this->has($field)) {
                $value = str_replace(',', '', $this->$field);
                $this->merge([$field => (float) $value]);
            }
        }
        
        // تأكد من أن التواريخ بصيغة Y-m-d
        if ($this->has('invoice_date')) {
            try {
                $date = Carbon::parse($this->invoice_date)->format('Y-m-d');
                $this->merge(['invoice_date' => $date]);
            } catch (\Exception $e) {
                // ترك التاريخ كما هو ليتم رفضه بالتحقق
            }
        }
        
        if ($this->has('due_date')) {
            try {
                $date = Carbon::parse($this->due_date)->format('Y-m-d');
                $this->merge(['due_date' => $date]);
            } catch (\Exception $e) {
                // ترك التاريخ كما هو ليتم رفضه بالتحقق
            }
        }
    }

    /**
     * التحقق الإضافي بعد القواعد الأساسية
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // التحقق من أن المبلغ الإجمالي = (المبلغ بعد الخصم + الضريبة)
            if ($this->has(['amount_commission', 'discount', 'value_vat', 'total'])) {
                $calculatedTotal = ($this->amount_commission - $this->discount) + $this->value_vat;
                
                // السماح بفرق بسيط بسبب التقريب
                $difference = abs($this->total - $calculatedTotal);
                
                if ($difference > 0.01) { // فرق أكثر من 0.01
                    $validator->errors()->add('total', 
                        'المبلغ الإجمالي غير صحيح. يجب أن يكون: (' . 
                        $this->amount_commission . ' - ' . $this->discount . ') + ' . 
                        $this->value_vat . ' = ' . number_format($calculatedTotal, 2));
                }
            }
            
            // التحقق من أن قيمة الضريبة تتناسب مع النسبة
            if ($this->has(['amount_collection', 'discount', 'rate_vat', 'value_vat'])) {
                $amountAfterDiscount = $this->amount_commission - $this->discount;
                $calculatedVat = ($amountAfterDiscount * $this->rate_vat) / 100;
                
                $difference = abs($this->value_vat - $calculatedVat);
                
                if ($difference > 0.01) {
                    $validator->errors()->add('value_vat',
                        'قيمة الضريبة غير صحيحة. يجب أن تكون: (' . 
                        number_format($amountAfterDiscount, 2) . ' × ' . 
                        $this->rate_vat . '%) ÷ 100 = ' . number_format($calculatedVat, 2));
                }
            }
            
            // التحقق من أن الخصم لا يتجاوز المبلغ
            if ($this->has(['amount_collection', 'discount'])) {
                if ($this->discount > $this->amount_collection) {
                    $validator->errors()->add('discount', 
                        'الخصم لا يمكن أن يتجاوز مبلغ التحصيل (' . $this->amount_collection . ')');
                }
            }
            
            // التحقق من أن العمولة لا تتجاوز المبلغ بعد الخصم
            if ($this->has(['amount_collection', 'discount', 'amount_commission'])) {
                $amountAfterDiscount = $this->amount_collection - $this->discount;
                if ($this->amount_commission > $amountAfterDiscount) {
                    $validator->errors()->add('amount_commission',
                        'العمولة لا يمكن أن تتجاوز المبلغ بعد الخصم (' . number_format($amountAfterDiscount, 2) . ')');
                }
            }
        });
    }
}