<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * تحديد إذا كان المستخدم مصرح له بتنفيذ هذا الطلب
     */
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');
        
        return Auth::check() && 
               Auth::user()->can('تعديل الفاتورة') ;
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        $invoiceId = $this->route('invoice')->id ?? null;
        $today = Carbon::today()->format('Y-m-d');
        $maxDate = Carbon::today()->addYears(5)->format('Y-m-d');
        
        return [
            // معلومات الفاتورة الأساسية
            'invoice_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('invoices', 'invoice_number')->ignore($invoiceId)
            ],
            
            'invoice_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:due_date',
                'after_or_equal:' . Carbon::today()->subMonths(3)->format('Y-m-d')
            ],
            
            'due_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:invoice_date',
                'before_or_equal:' . $maxDate
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
                'regex:/^\d+(\.\d{1,2})?$/'
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
                'min:0.01',
                'max:999999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            
            // الملاحظات
            'note' => [
                'nullable',
                'string',
                'max:1000'
            ],
            
            // المرفقات (إن وجدت للتحديث)
            'pic' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
                'max:10240',
                'dimensions:min_width=100,min_height=100'
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
        $invoice = $this->route('invoice');
        
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
            } else {
                // استخدام القيم الحالية إذا لم يتم إرسالها
                $this->merge([$field => $invoice->$field ?? 0]);
            }
        }
        
        // استخدام القيم الحالية للحقول المفقودة
        if (!$this->has('section_id')) {
            $this->merge(['section_id' => $invoice->section_id]);
        }
        
        if (!$this->has('product_id')) {
            $this->merge(['product_id' => $invoice->product_id]);
        }
        
        if (!$this->has('note')) {
            $this->merge(['note' => $invoice->note]);
        }
        
        // تأكد من أن التواريخ بصيغة Y-m-d
        $dateFields = ['invoice_date', 'due_date'];
        foreach ($dateFields as $field) {
            if ($this->has($field)) {
                try {
                    $date = Carbon::parse($this->$field)->format('Y-m-d');
                    $this->merge([$field => $date]);
                } catch (\Exception $e) {
                    // استخدام القيمة الحالية إذا فشل التحويل
                    if (!$this->has($field)) {
                        $this->merge([$field => $invoice->$field ?? null]);
                    }
                }
            } else {
                // استخدام التاريخ الحالي إذا لم يتم إرساله
                $this->merge([$field => $invoice->$field ?? null]);
            }
        }
        
        // استخدام رقم الفاتورة الحالي إذا لم يتم إرساله
        if (!$this->has('invoice_number') || empty($this->invoice_number)) {
            $this->merge(['invoice_number' => $invoice->invoice_number]);
        }
    }

    /**
     * التحقق الإضافي بعد القواعد الأساسية
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $invoice = $this->route('invoice');
            
            if (!$invoice) {
                return;
            }
            
            // التحقق من أن المبلغ الإجمالي = (المبلغ بعد الخصم + الضريبة)
            if ($this->has(['amount_commission', 'discount', 'value_vat', 'total'])) {
                $calculatedTotal = ($this->amount_commission - $this->discount) + $this->value_vat;
                
                // السماح بفرق بسيط بسبب التقريب
                $difference = abs($this->total - $calculatedTotal);
                
                if ($difference > 0.01) {
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
            
            // منع تعديل الفاتورة إذا كانت مرتبطة بمدفوعات وفشل التراجع
            // if ($this->has('invoice_number') && $this->invoice_number !== $invoice->invoice_number) {
            //     $hasPayments = $invoice->payments()->exists();
                
            //     if ($hasPayments) {
            //         $validator->errors()->add('invoice_number', 
            //             'لا يمكن تغيير رقم الفاتورة لأنها مرتبطة بمدفوعات');
            //     }
            // }
            
            // التحقق من أن المنتج ينتمي إلى القسم المحدد
            if ($this->has(['section_id', 'product_id'])) {
                $product = \App\Models\Product::find($this->product_id);
                
                if ($product && $product->section_id != $this->section_id) {
                    $validator->errors()->add('product_id', 
                        'المنتج المحدد لا ينتمي للقسم المختار');
                }
            }
            
            // // منع تخفيض المبالغ إذا كانت هناك مدفوعات
            // if ($invoice->payments()->exists()) {
            //     $totalPaid = $invoice->payments()->sum('amount');
                
            //     if ($this->has('total') && $this->total < $totalPaid) {
            //         $validator->errors()->add('total', 
            //             'لا يمكن تخفيض المبلغ الإجمالي إلى أقل من المبلغ المدفوع (' . 
            //             number_format($totalPaid, 2) . ')');
            //     }
            // }
        });
    }
}