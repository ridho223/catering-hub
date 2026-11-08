<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'package_type',
        'description',
        'price',
        'image',
        'status',
    ];

    public function details()
    {
        return $this->hasMany(PackageDetails::class);
    }

    /**
     * Jumlah porsi maksimum yang masih bisa dipesan saat ini
     * (berdasarkan stok menu penyusun paket). 0 = tidak bisa dipesan.
     */
    public function maxQuantity(): int
    {
        $this->loadMissing('details.menu');

        if ($this->details->isEmpty()) {
            return 0;
        }

        $max = (int) config('shop.max_quantity', 99);

        foreach ($this->details as $detail) {
            $menu = $detail->menu;
            $need = max(1, (int) $detail->quantity);

            if (! $menu || $menu->status !== 'Tersedia') {
                return 0;
            }

            $max = min($max, intdiv((int) $menu->stock, $need));
        }

        return max(0, $max);
    }

    public function isOrderable(): bool
    {
        return $this->status === 'Tersedia' && $this->maxQuantity() > 0;
    }

    public function orderPackages()
    {
        return $this->hasMany(OrderPackage::class);
    }
}