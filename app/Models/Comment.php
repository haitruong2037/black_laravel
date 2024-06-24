<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'admin_id',
        'reply_to_id',
        'content',
        'rate',
        'hidden'
    ];

    protected $appends = [
        'bought_product'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(Comment::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'reply_to_id');
    }

    public function getBoughtProductAttribute()
    {
        $bought_product = OrderDetail::where('product_id', $this->product_id)
                            ->whereHas('order', function ($query) {
                                $query->where('user_id', $this->user_id);
                            })->exists();
                            
        return $bought_product;
    }
}
