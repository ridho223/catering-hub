<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [ 
        'name',
        'description',
        'price',
        'image',
        'status',
    ];

    public function details()
    {
        return $this->hasMany(PackageDetails::class);
    }

    public function orderPackages()
    {
        return $this->hasMany(OrderPackage::class);
    }
}

