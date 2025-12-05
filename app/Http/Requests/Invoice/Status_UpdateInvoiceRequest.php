<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Status_UpdateInvoiceRequest extends FormRequest
{
    /**
     * تحديد إذا كان المستخدم مصرح له بتنفيذ هذا الطلب
     */
    public function authorize(): bool
    {
        $invoice = $this->route('invoice');
        
        return Auth::check() && 
               Auth::user()->can('تغير حالة الدفع');
    }

    /**
     * قواعد التحقق من البيانات
     */
    public function rules(): array
    {
        $invoice = $this->route('invoice');
        $invoiceDate = $invoice ? Carbon::parse($invoice->invoice_date) : null;
        $dueDate = $invoice ? Carbon::parse($invoice->due_date) : null;
        
        $rules = [
            'status' => [
                'required',
                'string',
                Rule::in(['مدفوعة', 'مدفوعة جزئيا', 'غير مدفوعة'])
            ],
            
            'note' => [
                'nullable',
                'string',
                'max:500'
            ]
        ];
        
        // إضافة قاعدة تاريخ الدفع إذا كانت الحالة مدفوعة أو مدفوعة جزئياً
        if ($this->status === 'مدفوعة' || $this->status === 'مدفوعة جزئيا') {
            $rules['payment_date'] = [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:' . ($invoiceDate ? $invoiceDate->format('Y-m-d') : Carbon::today()->subYears(1)->format('Y-m-d')),
                'before_or_equal:' . Carbon::today()->addDays(30)->format('Y-m-d')
            ];
            
            
            
            
        } else {
            // إذا كانت الحالة غير مدفوعة، لا نحتاج لتاريخ الدفع
            $rules['payment_date'] = [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ];
        }
        
        return $rules;
    }

    /**
     * رسائل الخطأ المخصصة
     */
    public function messages(): array
    {
        return [
            'status.required' => 'حالة الدفع مطلوبة',
            'status.in' => 'حالة الدفع غير صحيحة. المسموح: مدفوعة، مدفوعة جزئيا، غير مدفوعة',
            
            'payment_date.required' => 'تاريخ الدفع مطلوب للحالات المدفوعة',
            'payment_date.date' => 'تاريخ الدفع غير صحيح',
            'payment_date.date_format' => 'صيغة التاريخ يجب أن تكون YYYY-MM-DD',
            'payment_date.after_or_equal' => 'تاريخ الدفع يجب أن يكون بعد أو يساوي تاريخ الفاتورة',
            'payment_date.before_or_equal' => 'تاريخ الدفع لا يمكن أن يكون بعد 30 يوم من اليوم',
            
            
            'note.max' => 'الملاحظات يجب ألا تتجاوز 500 حرف'
        ];
    }

    /**
     * تسمية الحقول بالعربية
     */
    public function attributes(): array
    {
        return [
            'status' => 'حالة الدفع',
            'payment_date' => 'تاريخ الدفع',
            'note' => 'ملاحظات'
        ];
    }

    /**
     * تحضير البيانات قبل التحقق
     */
    protected function prepareForValidation(): void
    {
        $invoice = $this->route('invoice');
        
        // تحويل التواريخ إلى الصيغة الصحيحة
        if ($this->has('payment_date')) {
            try {
                $date = Carbon::parse($this->payment_date)->format('Y-m-d');
                $this->merge(['payment_date' => $date]);
            } catch (\Exception $e) {
                // ترك التاريخ كما هو ليتم رفضه بالتحقق
            }
        } else {
            // إذا لم يتم إرسال تاريخ الدفع وكانت الحالة مدفوعة، نستخدم تاريخ اليوم
            if ($this->status === 'مدفوعة' || $this->status === 'مدفوعة جزئيا') {
                $this->merge(['payment_date' => Carbon::today()->format('Y-m-d')]);
            }
        }
        
        // تنظيف الأرقام
        
        
        
        
        // تعيين القيمة الافتراضية للملاحظات إذا لم يتم إرسالها
        if (!$this->has('note') || empty($this->note)) {
            $this->merge(['note' => 'تم تحديث حالة الفاتورة إلى: ' . $this->status]);
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
            
            // التحقق من أن الفاتورة ليست محذوفة
            if ($invoice->trashed()) {
                $validator->errors()->add('status', 
                    'لا يمكن تحديث حالة فاتورة محذوفة');
                return;
            }
            
            // التحقق من عدم تغيير الحالة لنفس الحالة الحالية دون سبب
            if ($this->status === $invoice->status) {
                if (!$this->note || strpos($this->note, 'تم تحديث حالة الفاتورة') !== false) {
                    $validator->errors()->add('status', 
                        'الحالة الجديدة مطابقة للحالة الحالية. الرجاء إضافة ملاحظة توضح سبب التحديث');
                }
            }
            
            // التحقق من المدى الزمني لتاريخ الدفع
            if ($this->has('payment_date')) {
                $paymentDate = Carbon::parse($this->payment_date);
                $invoiceDate = Carbon::parse($invoice->invoice_date);
                $dueDate = Carbon::parse($invoice->due_date);
                
                // إذا كان تاريخ الدفع قبل تاريخ الفاتورة بأكثر من شهر
                if ($paymentDate->lt($invoiceDate->subMonth())) {
                    $validator->errors()->add('payment_date', 
                        'تاريخ الدفع لا يمكن أن يكون قبل تاريخ الفاتورة بأكثر من شهر');
                }
                
                // إذا كان تاريخ الدفع بعد تاريخ الاستحقاق بأكثر من سنة
                if ($paymentDate->gt($dueDate->addYear())) {
                    $validator->errors()->add('payment_date', 
                        'تاريخ الدفع لا يمكن أن يكون بعد تاريخ الاستحقاق بأكثر من سنة');
                }
            }
            
            
            
         
            
            // // منع تغيير الحالة من مدفوعة إلى غير مدفوعة
            if ($invoice->status === 'مدفوعة' && $this->status !== 'مدفوعة') {
                $validator->errors()->add('status', 
                    'لا يمكن تغيير حالة الفاتورة من "مدفوعة" إلى حالة أخرى');
            }
            
            // التحقق من تاريخ الدفع إذا كانت الحالة الحالية غير مدفوعة والجديدة مدفوعة
            if ($invoice->status === 'غير مدفوعة' && $this->status === 'مدفوعة') {
                if (!$this->has('payment_date') || empty($this->payment_date)) {
                    $validator->errors()->add('payment_date', 
                        'تاريخ الدفع مطلوب عند تغيير الحالة من غير مدفوعة إلى مدفوعة');
                }
            }
        });
    }
}