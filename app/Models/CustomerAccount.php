<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class CustomerAccount extends Authenticatable
{
    use Notifiable;

    protected $table = 'customer_accounts';

    protected $fillable = [
        'customer_id',
        'email',
        'password',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
