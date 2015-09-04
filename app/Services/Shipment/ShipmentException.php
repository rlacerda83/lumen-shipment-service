<?php

namespace App\Services\Shipment;

class ShipmentException extends \LogicException
{
    protected $fields;

    const MESSAGE = 'Invalid params!';

    public function __construct(array $fields, $code = 0, \Exception $previous = null)
    {
        $this->fields = $fields;
        parent::__construct(
            self::MESSAGE,
            $code,
            $previous
        );
    }

    public function getFields()
    {
        return $this->fields;
    }
}
