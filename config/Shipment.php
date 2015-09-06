<?php

// configuration options for all shippers
$config = [];

// can be 'LB' for pounds or 'KG' for kilograms
$config['weight_unit'] = env('SHIPMENT_WEIGHT_UNIT', 'KG');

// can be 'IN' for inches or 'CM' for centimeters
$config['dimension_unit'] = env('SHIPMENT_DIMENSION_UNIT', 'CM');

// BRL for R$
$config['currency_code'] = env('SHIPMENT_CURRENCY_CODE', 'BRL');

// if true and if a receiver email address is set, the tracking number will be emailed to the receiver by the
// shipping vendor
$config['email_tracking_number_to_receiver'] = env('SHIPMENT_EMAIL_TRACKING_NUMBER', true);

// shipper information
$config['shipper_name'] = env('SHIPMENT_SHIPPER_NAME', 'Rodrigo Lacerda');
$config['shipper_phone'] = env('SHIPMENT_SHIPPER_PHONE', '');
$config['shipper_email'] = env('SHIPMENT_SHIPPER_EMAIL', 'r.lacerda83@gmail.com');
$config['shipper_address1'] = env('SHIPMENT_SHIPPER_ADDRESS1', '');
$config['shipper_address2'] = env('SHIPMENT_SHIPPER_ADDRESS2', '');
$config['shipper_address3'] = env('SHIPMENT_SHIPPER_ADDRESS3', '');
$config['shipper_city'] = env('SHIPMENT_SHIPPER_CITY', 'São Paulo');
$config['shipper_state'] = env('SHIPMENT_SHIPPER_STATE', 'São Paulo');
$config['shipper_postal_code'] = env('SHIPMENT_SHIPPER_POSTAL_CODE', '05346000');
$config['shipper_country_code'] = env('SHIPMENT_SHIPPER_COUNTRY_CODE', 'BR');

return $config;
