<?php

namespace App\Services\Shipment\Shippers;

use App\Services\Shipment\Response;
use App\Services\Shipment\Shippers\ShippersAbstract;

class FlatRateShipping extends ShippersAbstract {

    const PRICE = '10.50';

    public function initConfig()
    {

    }

    public function getRate() {
        $rates = [];

        foreach ($this->services as $service) {
            $response = new Response();
            $response->setCode($service->code)
                ->setName('Flat Rate')
                ->setPrice(self::PRICE)
                ->setDeliveryTime(10);

            $rates[] = $response->toArray();
        }

        return $rates;
    }

    public function createLabel(array $params=array()) {

    }

}
