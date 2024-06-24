<?php

namespace App\Models;

use App\Helpers\ImageHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'discount',
        'quantity',
        'description',
        'view',
        'hot',
        'status',
    ];

    protected $appends = [
        'url_image',
        'image'
    ];

    public function productImages()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public function defaultImage()
    {
        return $this->hasOne(ProductImage::class, 'product_id')->orderBy('default', 'desc')->orderBy('id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function getUrlImageAttribute()
    {
        $image = $this->hasOne(ProductImage::class, 'product_id')->orderBy('default', 'desc')->orderBy('id')->first();
        return $image ? ImageHelper::getImageUrl($image->file_name, 'images/products/' . $this->id) : null;
    }

    public function getImageAttribute()
    {
        $image = $this->hasOne(ProductImage::class, 'product_id')->orderBy('default', 'desc')->orderBy('id')->first();
        return $image ? $image->file_name : null;
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'product_id');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_id');
    }
}
