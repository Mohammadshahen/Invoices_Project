<?php

namespace App\Models\Invoices;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'product_id',
        'section_id',
        'amount_collection',
        'amount_commission',
        'discount',
        'value_vat',
        'rate_vat',
        'total',
        'status',
        'value_status',
        'note',
        'payment_date',
    ];

        public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function detail()
    {
        return $this->hasMany(InvoiceDetail::class);
    }
    public function attachment()
    {
        return $this->hasMany(InvoiceAttachments::class);
    }
}
