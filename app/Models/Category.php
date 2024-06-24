<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
    ];
    protected $appends = ['url_image'];

    public function product()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function getUrlImageAttribute()
    {
        return asset('images/categories/' . $this->image);
    }
}
