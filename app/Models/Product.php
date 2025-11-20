<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['product_name','description','section_id'];
    
        public function section(): BelongsTo
    {
        return $this->belongsTo(section::class);
    }
}
