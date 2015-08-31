<?php

namespace App\Models;

use Elocache\Observers\BaseObserver;

class Carrier extends BaseModel
{

    protected $table = 'shipment_carriers';

    protected $fillable = ['document1', 'name', 'document2', 'address', 'city', 'state', 'postal_code', 'country', 'min_volume', 'max_volume', 'status'];

    public static function boot()
    {
        parent::boot();

        Self::observe(new BaseObserver());
    }
}
