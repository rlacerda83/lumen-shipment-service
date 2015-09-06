<?php

namespace App\Http\Controllers\V1;

use App\Models\Carrier;
use App\Models\Country;
use App\Repositories\Eloquent\CarrierRepository;
use App\Repositories\Eloquent\CountryRepository;
use App\Transformers\BaseTransformer;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\DeleteResourceFailedException;
use Laravel\Lumen\Routing\Controller as BaseController;
use QueryParser\QueryParserException;

class CarriersCountriesController extends BaseController
{
    use Helpers;

    /**
     * @var CountryRepository
     */
    private $repository;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @param CountryRepository $repository
     * @param CarrierRepository $carrierRepository
     */
    public function __construct(CountryRepository $repository, CarrierRepository $carrierRepository)
    {
        $this->repository = $repository;
        $this->carrierRepository = $carrierRepository;
    }

    /**
     * @return mixed
     */
    public function index(Request $request, $idCarrier)
    {
        $carrier = $this->carrierRepository->find($idCarrier);
        if (! $carrier) {
            throw new UpdateResourceFailedException('Carrier not found');
        }

        try {
            $paginator = $this->repository->findAllByCarrierPaginate($request, $idCarrier);

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
    public function create(Request $request, $idCarrier)
    {
        try {
            $carrier = $this->validCarrier($idCarrier);
            $this->carrierRepository->setModel($carrier);

            $countriesCodes = $request->input('countries', []);
            foreach ($countriesCodes as $code) {
                $country = $this->validCountry($code);
                $this->carrierRepository->addCountry($country);
            }

            return $this->response->created();
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $idCarrier
     * @return mixed
     */
    public function delete(Request $request, $idCarrier)
    {
        try {
            $carrier = $this->validCarrier($idCarrier);
            $this->carrierRepository->setModel($carrier);

            $countriesCodes = $request->input('countries', []);

            foreach ($countriesCodes as $code) {
                $country = $this->validCountry($code);
                $this->carrierRepository->removeCountry($country);
            }

            return $this->response->noContent();
        } catch (\Exception $e) {
            throw new DeleteResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param $idCarrier
     * @return mixed
     */
    protected function validCarrier($idCarrier)
    {
        $carrier = $this->carrierRepository->find($idCarrier);
        if (! $carrier) {
            throw new UpdateResourceFailedException("Carrier '{$idCarrier}' not found");
        }

        return $carrier;
    }

    /**
     * @param $code
     * @return mixed
     */
    protected function validCountry($code)
    {
        $country = $this->repository->findBy('code', $code);
        if (! $country) {
            throw new UpdateResourceFailedException("Country '{$code}' not found");
        }

        return $country;
    }
}
