<?php

namespace App\Services;

class Shipment {

    protected static $shipperMap = array(
        'PostOffice' => 'PO'
    );

    /**
     *
     * @var array holding package objects
     */
    protected $packages = array();

    /**
     *
     * @var array holding data specific to the shipment
     */
    protected $shipmentData = array();

    protected $shippers = array();


    /**
     * Constructor sets the object properties, sanitizes input and makes sure all required fields are set
     *
     * @param array $shipmentData the shipment data
     * @version 04/19/2013
     * @since 12/02/2012
     */
    public function __construct(array $shipmentData = array())
    {

    }

    /**
     * @param array $shipmentData
     * @throws \Exception
     */
    public function create(array $shipmentData = array())
    {
        // set object properties
        if(!is_array($shipmentData) || empty($shipmentData)) {
            throw new \Exception('Shipment Data array is empty.');
        }
        $this->shipmentData = $shipmentData;
        // sanitize $shipmentData values
        $this->sanitizeInput();
        // make sure that all required fields in $shipmentData are set
        $this->isShipmentValid();

    }


    public function getShippers()
    {
        if (null == $this->shippers) {
            $this->shippers = [];

            $path = __DIR__ . '/Shippers';
            $shippers = new \DirectoryIterator($path);
            foreach ($shippers as $file) {
                if ($file->isDot() || $file->isDir()) {
                    continue;
                }

                $this->shippers[] = array(
                    'code' => self::$shipperMap[$file->getBasename('.php')],
                    'name' => $file->getBasename('.php')
                );

            }
        }

        return $this->shippers;
    }


    /**
     * Adds a Package object to the class' $packages array
     *
     * @version updated 12/04/2012
     * @since 12/02/2012
     * @param object \Awsp\Ship\Package $package
     */
    public function addPackage(Package $package)
    {
        // add this package to the shipment's array
        $this->packages[] = $package;
    }


    /**
     * Returns the specified property of the object's shipmentData array.
     *
     * @version updated 01/01/2013
     * @since 12/08/2012
     * @param string $field the field of the desired property within the shipmentData array
     * @return mixed the value found for the specified field of the shipmentData array
     */
    public function get($field)
    {
        // as long as the field is set, return its value
        if(isset($this->shipmentData[$field])) {
            return $this->shipmentData[$field];
        }
    }


    /**
     * Returns the array containing the package(s) object(s) or throwns an exception if there are none.
     *
     * @version updated 01/01/2013
     * @since 12/08/2012
     * @return array containing all package object(s) that belong to this Shipment
     * @throws \UnexpectedValueException if the packages array is empty
     */
    public function getPackages()
    {
        // make sure that field of the array is set and throw an exception if it is not
        if(empty($this->packages)) {
            throw new \UnexpectedValueException('There is no data in the packages array.');
        }
        // as long as the field is set, return its value
        return $this->packages;
    }


    /**
     * Goes through each element of $this->shipmentData and applys some basic filtering to it.  The elements of
     *  $this->shipmentData are updated with the filtered results.
     *
     * @todo this is basic filtering - add additional filtering as necessary for your application
     * @return void
     * @version 01/14/2013
     * @since 01/14/2013
     */
    protected function sanitizeInput()
    {
        // go through all elements of the $shipmentData array and sanitize each value
        foreach($this->shipmentData as $key => $value) {
            // trim any whitespace
            $value = trim($value);
            // strip out any HTML or PHP
            $value = filter_var($value, FILTER_SANITIZE_STRING);
            // trim all input to maximum of 50 characters
            $value = substr($value, 0, 50);
            // update the array with the sanitized value
            $this->shipmentData[$key] = $value;
        }
    }


    /**
     * Makes sure that all required fields are set
     *
     * @return boolean true if all required fields are set
     * @throws \UnexpectedValueException if a required field is null
     * @version 01/14/2013
     * @since 01/14/2013
     */
    protected function isShipmentValid()
    {
        // create an array with the keys of $shipmentData that are required
        $required_fields = array('receiver_name', 'receiver_address1', 'receiver_city', 'receiver_state',
            'receiver_postal_code', 'receiver_country_code');

        // if shipment is being sent from an address other than the shippers, there are additional required fields
        if($this->shipmentData['ship_from_different_address'] == true) {
            array_push($required_fields, 'shipping_from_name', 'shipping_from_address1', 'shipping_from_city',
                'shipping_from_state', 'shipping_from_postal_code', 'shipping_from_country_code');
        }
        // create a variable to hold invalid properties
        $invalid_properties = null;
        // make sure that each of these keys has an acceptable value
        foreach($required_fields as $field) {
            // make sure the required field is not empty
            if($this->shipmentData[$field] == null) {
                // add this field to the list of invalid properties
                $invalid_properties .= $field . ', ';
            }
        }
        // if there are any invalid properties, throw an exception
        if(!empty($invalid_properties)) {
            throw new \Exception('Shipment object is not valid.  Required properties ('
                . $invalid_properties . ') are not set.');
        }
        else {
            return true;
        }
    }


}
