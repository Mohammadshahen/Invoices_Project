<?php

namespace App\Models\Invoices;

use App\Models\Product;
use App\Models\section;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InvoiceDetail extends Model
{
    protected $fillable = [
        'invoice_id',
        'invoice_number',
        'product_id',
        'section_id',
        'status',
        'value_status',
        'note',
        'user_id',
        'payment_date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function section()
    {
        return $this->belongsTo(section::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = Auth::id();
        });
    }
}
