<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'invoice_number'=>'required|unique:invoices,invoice_number',
            'invoice_date' => 'required|date|after_or_equal:today',
            'due_date' => 'required|date|after:invoice_date',
            'product_id' => 'required|exists:products,id',
            'section_id' => 'required|exists:sections,id',
            'amount_collection' => 'required|numeric|min:0',
            'amount_commission' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'rate_vat' => 'required|numeric|min:0|max:100',
            'value_vat' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            "status"=> "sometimes| in:مدفوعة,غير مدفوعة,مدفوعة جزئيا",
            "payment_date"=> "sometimes|date|after_or_equal:invoice_date",
            'pic'=>'sometimes|file|image|mimes:png,jpg,jpeg|max:10000|mimetypes:image/jpeg,image/png,image/jpg',
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_number.required'=>'يرجى ادخال رقم الفاتورة',
            'invoice_number.unique'=>'رقم الفاتورة مسجل مسبقا',
            'invoice_date.required'=>'يرجى ادخال تاريخ الفاتورة',
            'due_date.required'=>'يرجى ادخال تاريخ الاستحقاق',
            'product_id.required'=>'يرجى اختيار المنتج',
            'section_id.required'=>'يرجى اختيار القسم',
            'amount_collection.required'=>'يرجى ادخال مبلغ التحصيل',
            'amount_commission.required'=>'يرجى ادخال مبلغ العمولة',
            'discount.required'=>'يرجى ادخال الخصم',
            'rate_vat.required'=>'يرجى ادخال نسبة الضريبة',
            'value_vat.required'=>'يرجى ادخال قيمة الضريبة',
            'total.required'=>'يرجى ادخال المجموع الكلي',
        ];
    }
}
