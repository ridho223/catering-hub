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

    /**
     * Samakan format nomor telepon: 0812-3456 / +62 812 3456 / 62812... => 08123456
     */
    public static function normalizePhone(?string $phone): string
    {
        $p = preg_replace('/[^\d+]/', '', (string) $phone);

        if (str_starts_with($p, '+62')) {
            $p = '0' . substr($p, 3);
        } elseif (str_starts_with($p, '62')) {
            $p = '0' . substr($p, 2);
        }

        $p = str_replace('+', '', $p);

        if ($p !== '' && $p[0] === '8') {
            $p = '0' . $p;
        }

        return $p;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function account()
    {
        return $this->hasOne(CustomerAccount::class);
    }

}