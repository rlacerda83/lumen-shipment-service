<?php namespace App\Http\Controllers\V1;

use App\Models\Carrier;
use App\Services\Shipment;
use App\Services\Shippers\PostOffice;
use App\Transformers\CarrierTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\DeleteResourceFailedException;
use Laravel\Lumen\Routing\Controller as BaseController;

class CarriersController extends BaseController
{

    use Helpers;

    /**
     * @return mixed
     */
    public function index()
    {
        $paginator =  Carrier::orderBy('name')->paginate(10);
        return $this->response->paginator($paginator, new CarrierTransformer());
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {

        $carrier = new Carrier();
        $handleRequest = $carrier->validateRequest($request);

        if (is_array($handleRequest)) {
            throw new StoreResourceFailedException('Invalid params', $handleRequest);
        }

        try {

            $carrier = $carrier->create($request->all());
            return $this->response->item($carrier, new CarrierTransformer())->setStatusCode(201);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }

    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws UpdateResourceFailedException
     */
    public function update(Request $request, $id)
    {

        $carrier  = Carrier::find($id);
        if(!$carrier) {
            throw new UpdateResourceFailedException('Carrier not found');
        }

        try {
            $carrier->fill($request->all());
            $carrier->save();

            return $this->response->item($carrier, new CarrierTransformer);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function get($id)
    {

        $carrier = Carrier::find($id);
        if(!$carrier) {
            throw new StoreResourceFailedException('Carrier not found');
        }

        return $this->response->item($carrier, new CarrierTransformer);

    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id)
    {

        try {
            $carrier = Carrier::find($id);
            if(!$carrier) {
                throw new DeleteResourceFailedException('Carrier not found');
            }

            $carrier->delete();
            return $this->response->noContent();
        } catch (\Exception $e) {
            throw new DeleteResourceFailedException($e->getMessage());
        }

    }
}


