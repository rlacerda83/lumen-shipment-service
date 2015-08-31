<?php

namespace App\Models;

use Elocache\Observers\BaseObserver;

class CarrierService extends BaseModel
{

    protected $table = 'shipment_carriers_services';

    protected $fillable = ['carrier_id', 'code', 'name', 'description', 'delivery_time', 'status'];


    public static function boot()
    {
        parent::boot();

        Self::observe(new BaseObserver());
    }
}
