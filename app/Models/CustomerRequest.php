<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRequest extends Model
{
    protected $table= 'customer_requests';

    protected $fillable = [
        'customer_id',
        'order_id',
        'title',
        'description',
        'guest_count',
        'event_date',
        'address',
        'estimated_budget',
        'final_price',
        'admin_notes',
        'status',
        'rejected_by'
    ];

    protected $casts = [
        'event_date' => 'date',
        'estimated_budget' => 'decimal:2',
        'final_price' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function details()
    {
        return $this->hasMany(CustomerRequestDetail::class);
    }

    public function packages()
    {
        return $this->belongsToMany(
            Package::class, 'customer_request_packages'
        )->withPivot('quantity', 'price')->withTimestamps();
    }
}
