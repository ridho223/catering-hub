<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageDetails extends Model
{
    protected $fillable = [
        'package_id',
        'menus_id',
        'quantity',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class,'menus_id');
    }
}
