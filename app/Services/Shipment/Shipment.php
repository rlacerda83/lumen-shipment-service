<?php

namespace App\Services\Shipment;

use App\Models\Carrier;
use App\Repositories\Eloquent\CarrierRepository;
use Illuminate\Container\Container;

class Shipment
{
    /**
     * @var Package
     */
    protected $package;

    protected $carriers = [];

    //from data
    protected $fromName = null;

    protected $fromAddress1 = null;

    protected $fromCity = null;

    protected $fromState = null;

    protected $fromPostalCode = null;

    protected $fromCountryCode = null;

    // to data
    protected $toName = null;

    protected $toAddress1 = null;

    protected $toCity = null;

    protected $toState = null;

    protected $toPostalCode = null;

    protected $toCountryCode = null;

    /**
     * @var CarrierRepository
     */
    protected $carrierRepository;

    public function __construct()
    {
        $container = new Container();
        $this->carrierRepository = new CarrierRepository($container);
    }

    public function setFromName($fromName)
    {
        $this->fromName = $fromName;

        return $this;
    }

    public function getFromName()
    {
        return $this->fromName;
    }

    public function setFromAddress1($fromAddress1)
    {
        $this->fromAddress1 = $fromAddress1;

        return $this;
    }

    public function getFromAddress1()
    {
        return $this->fromAddress1;
    }

    public function setFromCity($fromCity)
    {
        $this->fromCity = $fromCity;

        return $this;
    }

    public function getFromCity()
    {
        return $this->fromCity;
    }

    public function setFromState($fromState)
    {
        $this->fromState = $fromState;

        return $this;
    }

    public function getFromState()
    {
        return $this->fromState;
    }

    public function setFromPostalCode($fromPostalCode)
    {
        $this->fromPostalCode = $fromPostalCode;

        return $this;
    }

    public function getFromPostalCode()
    {
        return $this->fromPostalCode;
    }

    public function setFromCountryCode($fromCountryCode)
    {
        $this->fromCountryCode = $fromCountryCode;

        return $this;
    }

    public function getFromCountryCode()
    {
        return $this->fromCountryCode;
    }

    public function setToName($toName)
    {
        $this->toName = $toName;

        return $this;
    }

    public function getToName()
    {
        return $this->toName;
    }

    public function setToAddress1($toAddress1)
    {
        $this->toAddress1 = $toAddress1;

        return $this;
    }

    public function getToAddress1()
    {
        return $this->toAddress1;
    }

    public function setToCity($toCity)
    {
        $this->toCity = $toCity;

        return $this;
    }

    public function getToCity()
    {
        return $this->toCity;
    }

    public function setToState($toState)
    {
        $this->toState = $toState;

        return $this;
    }

    public function getToState()
    {
        return $this->toState;
    }

    public function setToPostalCode($toPostalCode)
    {
        $this->toPostalCode = $toPostalCode;

        return $this;
    }

    public function getToPostalCode()
    {
        return $this->toPostalCode;
    }

    public function setToCountryCode($toCountryCode)
    {
        $this->toCountryCode = $toCountryCode;

        return $this;
    }

    public function getToCountryCode()
    {
        return $this->toCountryCode;
    }

    public function addCarrier(Carrier $carrier)
    {
        $this->carriers[] = $carrier;
    }

    /**
     * @param Package $package
     */
    public function setPackage(Package $package)
    {
        // add this package to the shipment's array
        $this->package = $package;
    }

    /**
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    public function getCarriers()
    {
        // make sure that field of the array is set and throw an exception if it is not
        if (empty($this->carriers)) {
            throw new \UnexpectedValueException('There is no data in the carriers array.');
        }
        // as long as the field is set, return its value
        return $this->carriers;
    }

    public function getRates()
    {
        $carrierRates = [];
        foreach ($this->carriers as $carrier) {
            $this->carrierRepository->setModel($carrier);

            $response = [];
            $response['code'] = $carrier->code;
            $response['name'] = $carrier->name;

            if (class_exists($carrier->model_reference)) {
                $shipper = new $carrier->model_reference($carrier, $this);
                $shipper->setServices($this->carrierRepository->getServices());
                $result = $shipper->getRate();

                if (isset($result['errors'])) {
                    $response['errors'] = $result['errors'];
                } else {
                    $response['rates'] = $result;
                }
            }

            $carrierRates[] = $response;
        }

        return $carrierRates;
    }
}
