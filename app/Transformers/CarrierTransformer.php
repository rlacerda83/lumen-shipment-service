<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class CarrierTransformer extends TransformerAbstract
{

    /**
     * @param \App\Models\Carrier $carrier
     * @return array
     */
    public function transform(\App\Models\Carrier $carrier)
    {
       return $carrier->toArray();
    }

}