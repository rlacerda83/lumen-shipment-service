<?php

namespace App\Models\Carrier;

use App\Models\BaseModel;
use Validator;

class Services extends BaseModel
{

    protected $table = 'shipment_carriers_services';

    protected $fillable = ['code', 'name', 'description', 'delivery_time', 'status'];


    /**
     * Get the carrier that owns the service.
     */
    public function carrier()
    {
        return $this->belongsTo('App\Models\Carrier');
    }

}
