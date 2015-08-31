<?php namespace App\Http\Controllers\V1;

use App\Models\Carrier;
use App\Repositories\Eloquent\CarrierRepository;
use App\Transformers\CarrierTransformer;
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
     * @param CarrierRepository $repository
     */
    public function __construct(CarrierRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            $paginator = $this->repository->findAllPaginate($request, 10);

            return $this->response->paginator($paginator, new CarrierTransformer);
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
            return $this->response->item($carrier, new CarrierTransformer())->setStatusCode(201);
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
        if(!$carrier) {
            throw new UpdateResourceFailedException('Carrier not found');
        }

        try {
            $carrier = $this->repository->update($request->all(), $carrier);

            return $this->response->item($carrier, new CarrierTransformer);
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
        if(!$carrier) {
            throw new StoreResourceFailedException('Carrier not found');
        }

        return $this->response->item($carrier, new CarrierTransformer);
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


