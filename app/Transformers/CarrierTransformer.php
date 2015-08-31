<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class CarrierTransformer extends TransformerAbstract
{

    /**
     * @param $carrier
     * @return mixed
     */
    public function transform($carrier)
    {
        if ($carrier instanceof \stdClass) {
            return json_decode(json_encode($carrier), true);
        }

        return $carrier->toArray();
    }

}