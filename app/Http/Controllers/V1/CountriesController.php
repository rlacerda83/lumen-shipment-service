<?php

namespace App\Http\Controllers\V1;

use App\Models\Country;
use App\Repositories\Eloquent\CountryRepository;
use App\Transformers\BaseTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Laravel\Lumen\Routing\Controller as BaseController;
use QueryParser\QueryParserException;

class CountriesController extends BaseController
{
    use Helpers;

    /**
     * @var CountryRepository
     */
    private $repository;

    /**
     * @param CountryRepository $repository
     */
    public function __construct(CountryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            $paginator = $this->repository->findAllPaginate($request);

            return $this->response->paginator($paginator, new BaseTransformer);
        } catch (QueryParserException $e) {
            throw new StoreResourceFailedException($e->getMessage(), $e->getFields());
        }
    }

    /**
     * @param $code
     * @return mixed
     */
    public function get($code)
    {
        $country = $this->repository->findBy('code', $code);
        if (! $country) {
            throw new StoreResourceFailedException("Country '{$code}' not found");
        }

        return $this->response->item($country, new BaseTransformer);
    }
}
