<?php

namespace App\Repositories\Eloquent;

use App\Models\Carrier;
use Elocache\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;
use QueryParser\ParserRequest;
use Illuminate\Container\Container as App;
use Validator;

class CarrierServiceRepository extends AbstractRepository
{

    protected $tableCarriers = null;
    protected $tableCarriersServices = null;

    public static $rules = [
        'name' => 'required|max:150',
        'carrier_id' => "required|exists:shipment_carriers,id",
        'code' => 'required'
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->tableCarriers = Carrier::getTableName();
        $this->tableCarriersServices = $this->getModel()->getTableName();
    }

    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return 'App\Models\CarrierService';
    }

    /**
     * @return mixed
     */
    public function getCarrier()
    {
        return $this->getModel()->belongsTo('App\Models\Carrier');
    }

    public function findByIdAndCarrier($id, Carrier $carrier)
    {
        $query = $this->queryBuilder
            ->select($this->tableCarriersServices.'.*')
            ->join($this->tableCarriers, $this->tableCarriers.'.id', '=', $this->tableCarriersServices.'.carrier_id')
            ->where($this->tableCarriersServices.'.id', $id)
            ->where($this->tableCarriersServices.'.carrier_id', $carrier->id);

        return $this->cacheQueryBuilder($id.$carrier->id, $query, 'first');
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function validateRequest(Request $request)
    {
        $rules = self::$rules;

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        return true;
    }

    /**
     * @param Request $request
     * @param $idCarrier
     * @param int $itemsPage
     *
     * @return mixed
     */
    public function findAllPaginate(Request $request, $idCarrier, $itemsPage = 30)
    {
        $key = md5($itemsPage.$request->getRequestUri());

        $query = $this->queryBuilder
            ->select($this->tableCarriersServices.'.*')
            ->join($this->tableCarriers, $this->tableCarriers.'.id', '=', $this->tableCarriersServices.'.carrier_id')
            ->where($this->tableCarriers.'.id', $idCarrier);

        $queryParser = new ParserRequest($request, $this->getModel(), $query);
        $queryBuilder = $queryParser->parser();

        return $this->cacheQueryBuilder($key, $queryBuilder, 'paginate', $itemsPage);
    }
}
