<?php

namespace App\Services\Shipment\Shippers;

use App\Services\Shipment\Shipment;
use App\Models\Carrier;

abstract class ShippersAbstract
{
    /**
     * @var Carrier
     */
    protected $carrier = null;

    protected $services = null;

    protected $config;

    /**
     * @var Shipment
     */
    protected $shipment;

    /**
     * @param Carrier $carrier
     * @param Shipment $shipment
     */
    public function __construct(Carrier $carrier, Shipment $shipment)
    {
        $this->config = $carrier->config;
        $this->carrier = $carrier;
        $this->setShipment($shipment);
        $this->initConfig();
    }

    /**
     * @param Shipment $shipment
     */
    public function setShipment(Shipment $shipment)
    {
        // validate the Shipment object
        if (! ($shipment instanceof Shipment)) {
            throw new \InvalidArgumentException('Shipment is not a valid object type.');
        }
        // set the object property
        $this->shipment = $shipment;
    }

    public function setServices($services)
    {
        $this->services = $services;
    }

    public function getServices()
    {
        return $this->services;
    }

    abstract public function initConfig();

    abstract public function getRate();

    abstract public function createLabel();
}
