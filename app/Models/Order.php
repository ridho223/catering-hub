<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'tracking_token',
        'customer_id',
        'order_type',
        'order_date',
        'delivery_date',
        'delivery_method',
        'delivery_fee',
        'delivery_distance',
        'status',
        'total',
        'notes',    
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'delivery_distance' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function packages()
    {
        return $this->hasMany(OrderPackage::class);
    }
    
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
