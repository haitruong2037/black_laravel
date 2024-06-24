<?php

namespace App\Models;

use App\Helpers\ImageHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'file_name',
        'default'
    ];

    protected $appends = [
        'url_image',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getUrlImageAttribute()
    {
        return $this->file_name ? ImageHelper::getImageUrl($this->file_name, 'images/products/' . $this->product_id) : null;

    }
}
