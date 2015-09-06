<?php

namespace App\Http\Controllers\V1;

use App\Models\Carrier;
use App\Models\Country;
use App\Repositories\Eloquent\CarrierRepository;
use App\Repositories\Eloquent\CountryRepository;
use App\Services\Shipment\Package;
use App\Services\Shipment\Shipment;
use App\Services\Shipment\ShipmentException;
use App\Transformers\BaseTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\DeleteResourceFailedException;
use Laravel\Lumen\Routing\Controller as BaseController;
use QueryParser\QueryParserException;

class CarriersController extends BaseController
{
    use Helpers;

    /**
     * @var CarrierRepository
     */
    private $repository;

    /**
     * @var CountryRepository
     */
    private $countryRepository;

    /**
     * @param CarrierRepository $repository
     */
    public function __construct(CarrierRepository $repository, CountryRepository $countryRepository)
    {
        $this->repository = $repository;
        $this->countryRepository = $countryRepository;
    }

    /**
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            $paginator = $this->repository->findAllPaginate($request, 10);

            return $this->response->paginator($paginator, new BaseTransformer);
        } catch (QueryParserException $e) {
            throw new StoreResourceFailedException($e->getMessage(), $e->getFields());
        }
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function create(Request $request)
    {
        $handleRequest = $this->repository->validateRequest($request);

        if (is_array($handleRequest)) {
            throw new StoreResourceFailedException('Invalid params', $handleRequest);
        }

        try {
            $carrier = $this->repository->create($request->all());

            return $this->response->item($carrier, new BaseTransformer)->setStatusCode(201);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     *
     * @return mixed
     * @throws UpdateResourceFailedException
     */
    public function update(Request $request, $id)
    {
        $carrier = $this->repository->find($id);
        if (! $carrier) {
            throw new UpdateResourceFailedException('Carrier not found');
        }

        try {
            $carrier = $this->repository->update($request->all(), $carrier);

            return $this->response->item($carrier, new BaseTransformer);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function get($id)
    {
        $carrier = $this->repository->find($id);
        if (! $carrier) {
            throw new StoreResourceFailedException('Carrier not found');
        }

        return $this->response->item($carrier, new BaseTransformer);
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id)
    {
        try {
            $carrier = $this->repository->find($id);
            if (! $carrier) {
                throw new DeleteResourceFailedException('Carrier not found');
            }

            $carrier->delete();

            return $this->response->noContent();
        } catch (\Exception $e) {
            throw new DeleteResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function getAllRates(Request $request)
    {
        $shipment = new Shipment();

        //Destination data
        $shipment->setToPostalCode($request->input('to_zip', ''))
            ->setToName($request->input('to_name', ''))
            ->setToAddress1($request->input('to_address', ''))
            ->setToCity($request->input('to_city', ''))
            ->setToState($request->input('to_state', ''))
            ->setToCountryCode($request->input('to_country', ''));

        //sender data
        $shipment->setFromPostalCode($request->input('from_zip', env('SHIPMENT_FROM_POSTAL_CODE')))
            ->setFromName($request->input('from_name', env('SHIPMENT_FROM_NAME')))
            ->setFromAddress1($request->input('from_address', env('SHIPMENT_FROM_ADDRESS1')))
            ->setFromCity($request->input('from_city', env('SHIPMENT_FROM_CITY')))
            ->setFromState($request->input('from_state', env('SHIPMENT_FROM_STATE')))
            ->setFromCountryCode($request->input('from_country', env('SHIPMENT_FROM_COUNTRY_CODE')));

        $country = $this->countryRepository->findBy('code', $shipment->getToCountryCode());
        if (! $country) {
            throw new StoreResourceFailedException('Invalid country. Code "'.$shipment->getToCountryCode().'" not found');
        }

        $carriers = $this->repository->allWithCountry($country);
        foreach ($carriers as $carrier) {
            $shipment->addCarrier($carrier);
        }

        try {
            $package = new Package();

            $package->setWeight(floatval($request->input('parcel_weight', '')))
                ->setHeight(floatval($request->input('parcel_height', '')))
                ->setLength(floatval($request->input('parcel_length', '')))
                ->setWidth(floatval($request->input('parcel_width', '')));

            $shipment->setPackage($package);
            $rates = $shipment->getRates();
        } catch (ShipmentException $e) {
            throw new StoreResourceFailedException($e->getMessage(), $e->getFields());
        }

        return response()->json(['data' => $rates]);
    }
}
