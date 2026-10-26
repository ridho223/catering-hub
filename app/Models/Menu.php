<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Menu extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'price',
        'stock',
        'description',
        'image',
        'status'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function packageDetail()
    {
        return $this->hasMany(PackageDetails::class,'menus_id');
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function customerRequestDetail()
    {
        return $this->hasMany(CustomerRequestDetail::class);
    }
}
