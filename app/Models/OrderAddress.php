<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'name',
        'address',
        'phone'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
