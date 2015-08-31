<?php namespace App\Http\Controllers\V1;

use App\Models\Carrier;
use App\Services\Shipment;
use App\Services\Shippers\PostOffice;
use App\Transformers\CarrierTransformer;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\DeleteResourceFailedException;
use Laravel\Lumen\Routing\Controller as BaseController;

class ShipmentController extends BaseController
{

    use Helpers;

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rate(Request $request)
    {
        /*
         * $required_fields = array('receiver_name', 'receiver_address1', 'receiver_city', 'receiver_state',
            'receiver_postal_code', 'receiver_country_code');
         */
        $shipment = new Shipment(array(
            'ship_from_different_address' => false,
            'receiver_name' => 'Rodrigo',
            'receiver_address1' => 'Rodrigo',
            'receiver_city' => 'São Paulo',
            'receiver_state' => 'SP',
            'receiver_postal_code' => '05346000',
            'receiver_country_code' => 'BR',
        ));

        $shipper = new PostOffice($shipment);
        print_r($shipment->getShippers());
        print_r($shipper->getServices()); die;
    }

    public function services(Request $request)
    {

    }

    public function shippers()
    {
        $shipment = new Shipment();
        return $this->response->array($shipment->getShippers());
    }

}


