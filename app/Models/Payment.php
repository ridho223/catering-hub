<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Payment extends Model
{
    protected $fillable = [
        'order_id',        
        'method',
        'amount',
        'status',
        'proof',
        'paid_at',
        'notes',
        'claimed_amount',
    ];

    
    protected function casts():array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'claimed_amount' => 'decimal:2'
        ];
    }


    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Label dihitung dari urutan pembayaran, bukan dari kolom type
    public function kindLabel(): string
    {
        $total  = (float) ($this->order->total ?? 0);
        $before = (float) Payment::where('order_id', $this->order_id)
            ->where('status', 'Dibayar')
            ->where('id', '<', $this->id)
            ->sum('amount');
        $amount = (float) $this->amount;

        if ($before <= 0) {
            return $amount >= $total - 0.5 ? 'Bayar lunas' : 'DP';
        }

        return ($before + $amount) >= $total - 0.5 ? 'Pelunasan' : 'Cicilan';
    }
}
