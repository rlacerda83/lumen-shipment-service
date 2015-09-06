<?php namespace App\Http\Controllers\V1;

use App\Models\Carrier;
use App\Models\CarrierService;
use App\Repositories\Eloquent\CarrierRepository;
use App\Repositories\Eloquent\CarrierServiceRepository;
use App\Services\Shipment;
use App\Transformers\BaseTransformer;
use Dingo\Api\Exception\UpdateResourceFailedException;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\DeleteResourceFailedException;
use Laravel\Lumen\Routing\Controller as BaseController;
use QueryParser\QueryParserException;

class CarriersServicesController extends BaseController
{

    use Helpers;

    /**
     * @var CarrierServiceRepository
     */
    private $repository;

    /**
     * @var CarrierRepository
     */
    private $carrierRepository;

    /**
     * @param CarrierServiceRepository $repository
     */
    public function __construct(CarrierServiceRepository $repository, CarrierRepository $carrierRepository)
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
        if(!$carrier) {
            throw new UpdateResourceFailedException('Carrier not found');
        }

        try {
            $paginator = $this->repository->findAllPaginate($request, $idCarrier);

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
        $request->merge(['carrier_id' => $idCarrier]);
        $handleRequest = $this->repository->validateRequest($request);

        if (is_array($handleRequest)) {
            throw new StoreResourceFailedException('Invalid params', $handleRequest);
        }

        try {
            $service = $this->repository->create($request->all());
            return $this->response->item($service, new BaseTransformer)->setStatusCode(201);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $idCarrier
     * @param $idService
     *
     * @return mixed
     */
    public function update(Request $request, $idCarrier, $idService)
    {
        $service = $this->getService($idCarrier, $idService);

        try {
            $request->merge(['carrier_id' => $idCarrier]);
            $service = $this->repository->update($request->all(), $service);

            return $this->response->item($service, new BaseTransformer);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param $idCarrier
     * @param $idService
     *
     * @return mixed
     */
    public function get($idCarrier, $idService)
    {
        $service = $this->getService($idCarrier, $idService);

        return $this->response->item($service, new BaseTransformer);
    }

    /**
     * @param $idCarrier
     * @param $idService
     *
     * @return CarrierService
     */
    protected function getService($idCarrier, $idService)
    {
        $carrier = $this->carrierRepository->find($idCarrier);
        if(!$carrier) {
            throw new UpdateResourceFailedException('Carrier not found');
        }

        $service = $this->repository->findByIdAndCarrier($idService, $carrier);
        if(!$service) {
            throw new UpdateResourceFailedException('Service not found');
        }

        return $service;
    }

    /**
     * @param $idCarrier
     * @param $idService
     *
     * @return mixed
     */
    public function delete($idCarrier, $idService)
    {
        try {
            $service = $this->getService($idCarrier, $idService);
            if(!$service) {
                throw new DeleteResourceFailedException('Carrier not found');
            }

            $service->delete();
            return $this->response->noContent();
        } catch (\Exception $e) {
            throw new DeleteResourceFailedException($e->getMessage());
        }
    }
}


