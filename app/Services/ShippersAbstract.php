<?php

namespace App\Services;

use App\Services\Shipment;

Abstract class ShippersAbstract {

    /**
     *
     * @var Shipment
     */
    protected $shipment = null;

    protected $services = null;

    protected $config;


    public function __construct()
    {
        $fullClassName = get_class($this);
        $classNameAux = explode('\\', $fullClassName);
        $className = array_pop($classNameAux);

        $pathConfig = __DIR__ . "/../../config/Shippers/{$className}.php";
        if (!file_exists($pathConfig)) {
            throw new \Exception('Config file not found');
        }

        $this->config = require_once($pathConfig);
    }

    /**
     * @param Shipment $Shipment
     */
    public function setShipment(Shipment $Shipment) {
        // validate the Shipment object
        if(!($Shipment instanceof Shipment)) {
            throw new \InvalidArgumentException('Shipment is not a valid object type.');
        }
        // set the object property
        $this->shipment = $Shipment;
    }

    public function getServices($onlyActives = false)
    {
        $arrayServices = array();
        foreach ($this->config['services'] as $service) {
            if($onlyActives) {
                if($service['enabled'] == true) $arrayServices[] = $service;
            } else {
                $arrayServices[] = $service;
            }

        }

        return $arrayServices;
    }


    abstract public function getRate();


    abstract public function createLabel();

}
