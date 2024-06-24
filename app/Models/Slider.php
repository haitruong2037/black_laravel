<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'is_published',
        'product_id',
    ];

    public function product()
    {
        return $this->hasOne(Product::class, 'product_id');
    }
}
