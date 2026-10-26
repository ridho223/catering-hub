<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function account()
    {
        return $this->hasOne(CustomerAccount::class);
    }

    public function customRequest()
    {
        return $this->hasMany(CustomerRequest::class);
    }
}
