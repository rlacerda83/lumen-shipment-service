## Shipping API

This is a simple API for shipping.
Status: under development

## Installation

Follow the steps below to install the service:

- Clone repository
- Run composer install
- Leave your .env file like this model:
    ```
    APP_ENV=local
    APP_DEBUG=true
    APP_KEY=key_with_32_characters
    
    APP_LOCALE=en
    APP_FALLBACK_LOCALE=en
    
    DB_CONNECTION=mysql
    DB_HOST=your_host
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=you_password
    
    CACHE_DRIVER=array
    SESSION_DRIVER=cookie
    QUEUE_DRIVER=database
    
    # SHIPMENT CONFIGURARION
    SHIPMENT_WEIGHT_UNIT=KG
    SHIPMENT_DIMENSION_UNIT=CM
    SHIPMENT_CURRENCY_CODE=BRL
    SHIPMENT_EMAIL_TRACKING_NUMBER=true
    
    SHIPMENT_SHIPPER_NAME=name
    SHIPMENT_SHIPPER_PHONE=
    SHIPMENT_SHIPPER_EMAIL=email
    SHIPMENT_SHIPPER_ADDRESS1=
    SHIPMENT_SHIPPER_ADDRESS2=
    SHIPMENT_SHIPPER_ADDRESS3=
    SHIPMENT_SHIPPER_CITY=São Paulo
    SHIPMENT_SHIPPER_STATE=SP
    SHIPMENT_SHIPPER_POSTAL_CODE=05346000
    SHIPMENT_SHIPPER_COUNTRY_CODE=BR
    
    # API CONFIGURATION
    API_VENDOR=app
    API_PREFIX=api
    API_VERSION=v1
    API_NAME=Shipment
    API_CONDITIONAL_REQUEST=false
    API_STRICT=false
    API_DEFAULT_FORMAT=json
    API_DEBUG=false
    ```
- Run migrations from console:
    ```
    php artisan migrate
    
    ```

## Making request to API

Making a request to your API is quite simple. The best way to do this is using a tool such as [Postman](http://www.getpostman.com/).

Because we aren't versioning the API in the URI we need to define an Accept header to request a specific version. The header is formatted like so.

```
Accept: application/vnd.YOUR_VENDOR.v1+json

```

In the above example you would replace YOUR_VENDOR with the vendor name you defined in your .env configuration. Again, this is usually something unique to your application, such as its name or identifier, and is usually all lowercase.

Following the vendor name we have the version we want. In the above example we're requesting v1 of our API. This is then followed by a plus sign and the desired format. If the format is invalid the package will attempt to use the default format you defined in your configuration.
