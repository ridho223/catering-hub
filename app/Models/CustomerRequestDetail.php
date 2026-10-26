<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerRequestDetail extends Model
{   
    protected $table = 'custom_request_details'; 

     protected $fillable = [
        'customer_request_id',
        'menu_id',
        'quantity',
        'requested_price',
        'approved_price',
        'customization_type',
        'replaces_menu_id',
        'notes',
    ];

    public function customerRequest(): BelongsTo
    {
        return $this->belongsTo(CustomerRequest::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function replacesMenu()
    {
        return $this->belongsTo(Menu::class,'replaces_menu_id');
    }
}
