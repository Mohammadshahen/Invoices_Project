<?php

namespace App\Models\Invoices;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class InvoiceAttachments extends Model
{
    protected $fillable = [
        'invoice_id',
        'user_id',
        'file_name',
    ];  


    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
