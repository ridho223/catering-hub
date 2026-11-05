<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code',
        'tracking_token',
        'customer_id',        
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
    
    public const MIN_PAYMENT_PERCENT = 40;

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function paidAmount(): float
    {
        return (float) $this->payments->where('status', 'Dibayar')->sum('amount');
    }

    public function remainingAmount(): float
    {
        return max(0, (float) $this->total - $this->paidAmount());
    }

    public function isFullyPaid(): bool
    {
        return (float) $this->total > 0 && $this->remainingAmount() < 1;
    }

    public function hasPendingPayment(): bool
    {
        return $this->payments->where('status', 'Menunggu Verifikasi')->isNotEmpty();
    }

    // Nominal minimum yang boleh dibayar sekarang
    public function minPayment(): float
    {
        $minimal = (float) ceil((float) $this->total * self::MIN_PAYMENT_PERCENT / 100);

        return min($minimal, $this->remainingAmount());
    }

    // Nominal maksimum yang boleh dibayar sekarang (= sisa tagihan)
    public function maxPayment(): float
    {
        return $this->remainingAmount();
    }

    public function paymentState(): string
    {
        if ($this->isFullyPaid())       return 'Dibayar';
        if ($this->hasPendingPayment()) return 'Menunggu Verifikasi';
        if ($this->payments->sortByDesc('id')->first()?->status === 'Ditolak') return 'Ditolak';
        if ($this->paidAmount() > 0)    return 'Sebagian';
        return 'Belum Dibayar';
    }

    public function pendingAmount(): float
    {
        return (float) $this->payments->where('status', 'Menunggu Verifikasi')->sum('amount');
    }
}
