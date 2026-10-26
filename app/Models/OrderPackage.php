<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPackage extends Model
{
    protected $fillable = [
        'order_id',
        'package_id',
        'quantity',
        'price',
        'subtotal'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
