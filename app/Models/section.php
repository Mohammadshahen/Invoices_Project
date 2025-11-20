<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class section extends Model
{
    public $fillable = [
        'section_name',
        'description',
        'create_by',
    ];

protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->user_id = Auth::id();
        });
    }


            public function product(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
