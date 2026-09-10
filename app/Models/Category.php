<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Menu;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',      
    ];

    public function menus()
{
    return $this->hasMany(Menu::class);
}

}


