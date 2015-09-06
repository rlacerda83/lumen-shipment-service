<?php

namespace App\Services\Shipment;

class Response
{
    protected $code = '';

    protected $name = '';

    protected $price = 0;

    protected $deliveryTime = 0;

    protected $error = '';

    protected $messageError = '';

    public function toArray()
    {
        $reflectionClass = new \ReflectionClass(get_class($this));
        $array = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $array[$property->getName()] = $property->getValue($this);
            $property->setAccessible(false);
        }

        return $array;
    }

    public function setCode($code)
    {
        $this->code = (string) $code;

        return $this;
    }

    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    public function setPrice($price)
    {
        $this->price = number_format((float) str_replace(',', '.', $price), 2);

        return $this;
    }

    public function setDeliveryTime($deliveryTime)
    {
        $this->deliveryTime = (integer) $deliveryTime;

        return $this;
    }

    public function setError($error)
    {
        $this->error = (string) $error;

        return $this;
    }

    public function setMessageError($messageError)
    {
        $this->messageError = (string) $messageError;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getDeliveryTime()
    {
        return $this->deliveryTime;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getMessageError()
    {
        return $this->messageError;
    }
}
